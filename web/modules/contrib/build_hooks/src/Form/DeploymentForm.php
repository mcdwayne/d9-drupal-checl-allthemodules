<?php

namespace Drupal\build_hooks\Form;

use Drupal\build_hooks\DeployLogger;
use Drupal\build_hooks\Entity\FrontendEnvironment;
use Drupal\build_hooks\TriggerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DeploymentForm.
 */
class DeploymentForm extends FormBase {

  /**
   * Drupal\build_hooks\TriggerInterface definition.
   *
   * @var \Drupal\build_hooks\TriggerInterface
   */
  protected $buildHooksTrigger;

  /**
   * Drupal\build_hooks\DeployLogger definition.
   *
   * @var \Drupal\build_hooks\DeployLogger
   */
  protected $buildHooksDeploylogger;

  /**
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Drupal\Core\Datetime\DateFormatter definition.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a new DeploymentForm object.
   */
  public function __construct(
    TriggerInterface $build_hooks_trigger,
    DeployLogger $build_hooks_deploylogger,
    Renderer $renderer,
    DateFormatter $dateFormatter
  ) {
    $this->buildHooksTrigger = $build_hooks_trigger;
    $this->buildHooksDeploylogger = $build_hooks_deploylogger;
    $this->renderer = $renderer;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * Create.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   *
   * @return \Drupal\build_hooks\Form\DeploymentForm
   *   The deployment form.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('build_hooks.trigger'),
      $container->get('build_hooks.deploylogger'),
      $container->get('renderer'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'deployment_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, FrontendEnvironment $frontend_environment = NULL) {
    // TODO: render this with some theme hook instead of html tags.
    $form['display'] = [
      '#markup' => '<h2>' . $this->t('@envName Environment', ['@envName' => $frontend_environment->label()]) . '</h2>',
    ];

    $form['environment_link'] = [
      '#markup' => $this->t('Frontend @environmentName site url: @link', [
        '@link' => Link::fromTextAndUrl($frontend_environment->getUrl(), Url::fromUri($frontend_environment->getUrl(), ['attributes' => ['target' => '_blank']]))
          ->toString(),
        '@environmentName' => $frontend_environment->label(),
      ]),
    ];

    // When was the last deployment?
    $last_deployment_timestamp = $this->buildHooksDeploylogger->getLastDeployTimeForEnvironment($frontend_environment);
    if ($last_deployment_timestamp) {
      $last_deployment_timestamp_formatted = $this->dateFormatter->format($last_deployment_timestamp, 'long');
      $form['last_deployment'] = [
        '#markup' => '<p>' . $this->t('Last deployment triggered on: <strong>@date</strong>', ['@date' => $last_deployment_timestamp_formatted]) . '</p>',
      ];
    }

    $form['changelog'] = [
      '#type' => 'details',
      '#title' => $this->t('Changelog'),
      '#description' => $this->t("This is a summary of the changes since the previous deployment to the <strong>%name</strong> environment:", ['%name' => $frontend_environment->label()]) . '</p>',
      '#open' => TRUE,
    ];

    // Have we logged any changes since last deployment?
    if ($this->buildHooksDeploylogger->getNumberOfItemsSinceLastDeploymentForEnvironment($frontend_environment) > 0) {
      try {
        $form['changelog']['log'] = [
          '#markup' => $this->getChangelogView($last_deployment_timestamp),
        ];
      }
      catch (\Exception $e) {
        $this->messenger()
          ->addWarning($this->t('Could not render the view with the changelog. Check configuration.'));
      }

    }
    else {
      $form['changelog']['#description'] = '<p>' . $this->t('No changes recorded since the last deployment for this environment. If needed you can still trigger a deployment using this page.') . '</p>';
    }

    // Add the entity to the form:
    $form['frontend_environment'] = [
      '#type' => 'value',
      '#value' => $frontend_environment,
    ];

    // Plugins have a possibility to return additional elements for this form:
    /** @var \Drupal\build_hooks\Plugin\FrontendEnvironmentBase $plugin */
    $plugin = $frontend_environment->getPlugin();
    $additionalFormElements = $plugin->getAdditionalDeployFormElements();

    // If they do, merge their form elements into the form:
    if (!empty($additionalFormElements)) {
      $form = NestedArray::mergeDeep(
        $form,
        $additionalFormElements);
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Start a new deployment to the @environment environment', ['@environment' => $frontend_environment->label()]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the environment entity:
    /** @var \Drupal\build_hooks\Entity\FrontendEnvironment $frontend_environment */
    $frontend_environment = $form_state->getValue('frontend_environment');
    $this->buildHooksTrigger->triggerBuildHookForEnvironment($frontend_environment);
  }

  /**
   * Use the included view to render the changelog.
   *
   * @param int $timestamp
   *   Timestamp argument to get the changelog starting from.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The rendered results.
   *
   * @throws \Exception
   */
  private function getChangelogView($timestamp) {
    $changelog_view = Views::getView('build_hooks_editing_log');
    $wids = $this->buildHooksDeploylogger->getLogItemsSinceTimestamp($timestamp);
    $arg = implode('+', $wids);
    $renderable_view = $changelog_view->buildRenderable('embed_1', [$arg]);
    return $this->renderer->render($renderable_view);
  }

}

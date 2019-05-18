<?php

namespace Drupal\config_pages\Command;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class GetValueCommand.
 *
 * @DrupalCommand (
 *     extension="config_pages",
 *     extensionType="module"
 * )
 */
class GetValueCommand extends ContainerAwareCommand {

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs a new GetValueCommand object.
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->logger = $loggerChannelFactory->get('config_pages');
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('config_pages:get_value')
      ->setDescription($this->trans('commands.config_pages.get_value.description'))
      ->addArgument(
        'bundle',
        InputArgument::REQUIRED,
        $this->trans('commands.user.login.url.options.bundle'),
        null
      )
      ->addArgument(
        'field_name',
        InputArgument::REQUIRED,
        $this->trans('commands.user.login.url.options.field_name'),
        null
      )
      ->addArgument(
        'context',
        InputArgument::OPTIONAL,
        $this->trans('commands.user.login.url.options.context'),
        null
      )->setAliases(['cpgfv']);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $bundle = $input->getArgument('bundle');
    $field_name = $input->getArgument('field_name');
    $context = $input->getArgument('context');

    try {
      $config_page = config_pages_config($bundle, $context);

      if (!empty($config_page)) {
        $output->writeln($config_page->get($field_name)->value);
      }
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }

  }
}

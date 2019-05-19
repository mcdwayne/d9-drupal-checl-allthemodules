<?php

namespace Drupal\simple_dialog_example\Controller;

class SimpleDialogExampleController {

  /**
   * @todo - Render from twig template diretly?
   */
  public function examplesPageContent() {
    $output = '<h2>HTML Implementation</h2>';
    $output .= '<p>Here are some examples of implementations using just html mark-up.</p>';
    $output .= '<p>This first one is the simplest implementation using no specific jQuery ui settings:</p>';
    $output .= '<p><pre>
    &lt;a href="'.base_path().'simple-dialog-example/target-page" class="simple-dialog" name="load-this-element" title="My Dialog Title"&gt;Click Me To See The Dialog&lt;/a&gt;</pre></p>';
    $output .= '<p><a href="'.base_path().'simple-dialog-example/target-page" class="simple-dialog" name="load-this-element" title="My Dialog Title">Click Me To See The Dialog</a></p>';
    $output .= '<p>This implements some custom jQuery ui dialog settings in the "rel" tag of the anchor element:</p>';
    $output .= '<p><pre>
    &lt;a href="'.base_path().'simple-dialog-example/target-page" class="simple-dialog" name="load-this-element" title="My Dialog Title" rel="width:900;resizable:false;position:[center,60];"&gt;Click Me To See The Dialog&lt;/a&gt;</pre></p>';
    $output .= '<p><a href="'.base_path().'simple-dialog-example/target-page" class="simple-dialog" name="load-this-element" title="My Dialog Title" rel="width:900;resizable:false;position:[center,60];">Click Me To See The Dialog</a></p>';
    $output .= '<h2>Theme Implementation</h2>';
    $output .= '<p>This is an example outputting the dialog link using the simple_dialog_link theme implementation:</p>';
    $output .= '<p><pre>
$element = ' . "array(
  '#theme' => 'simple_dialog_link',
  '#text' => 'Click Me To See The Dialog',
  '#path' => '/simple-dialog-example/target-page',
  '#selector' => 'load-this-element',
  '#title' => 'My Dialog Title',
  '#options' => array(
    'width' => 900,
    'resizable' => FALSE,
    'position' => array('center', 60) // can be an array of xy values
  ),
  '#class' => array('my-link-class'),
);
drupal_render(\$element);
</pre></p>";

    $element = array(
      '#theme' => 'simple_dialog_link',
      '#text' => 'Click Me To See The Dialog',
      '#path' => '/simple-dialog-example/target-page',
      '#selector' => 'load-this-element',
      '#title' => 'My Dialog Title',
      '#options' => array(
        'width' => 900,
        'resizable' => FALSE,
        'position' => array('center', 60) // can be an array of xy values
      ),
      '#class' => array('my-link-class'),
    );

    $output .= '<p>' . drupal_render($element) . '</p>';

    return array(
      '#type' => 'markup',
      '#markup' => $output,
    );
  }

  public function targetPageContent() {
    return array(
      '#type' => 'markup',
      '#markup' => '<div id="load-this-element"><h3>This is my target text to load</h3><p>Mauris pulvinar. Elementum sed a aliquam ut placerat et, aliquam, non, nisi aenean nec elementum in, nascetur scelerisque, magna eros, turpis non sagittis vel ultrices. Augue penatibus mid porttitor risus adipiscing sociis ac massa, vel, porttitor, urna urna, ultrices tincidunt sociis odio, est vel! Lorem odio placerat ac sagittis nisi, ac augue nunc cursus! Phasellus rhoncus? Duis augue in augue vut natoque tristique, dapibus lacus ultrices et cursus nascetur! Et in pellentesque, odio natoque non non adipiscing, augue magna hac enim, habitasse hac dictumst magnis, mus auctor, porttitor? Est augue, pulvinar et tincidunt sit quis nunc et proin eu duis amet et in ridiculus vel sociis, duis est et odio, quis? Scelerisque dictumst in lundium lectus dapibus et egestas, phasellus.</p><p>Porttitor elementum nunc sagittis odio! Purus velit mauris ridiculus, habitasse mus, placerat ut sociis lectus, magna sociis a et! Rhoncus magnis natoque massa, ridiculus augue augue. Dapibus, scelerisque proin nunc sociis, ultrices platea urna? Massa odio tincidunt facilisis turpis! Nisi in, natoque sed in et scelerisque urna vut a? In augue porta urna mauris aliquam duis a massa nec, tortor magna ridiculus lorem! Sociis tincidunt integer massa, enim augue sit phasellus, mauris in egestas, mattis, ultrices purus porttitor rhoncus. Sociis, vel, turpis dictumst. Lorem sit tincidunt sed porta, et, penatibus dapibus augue sed sed platea tincidunt turpis cum habitasse habitasse porta, magna purus, sociis. Lectus urna rhoncus? Augue aliquam proin, non lacus massa, placerat elit. Lundium! Odio magnis pellentesque.</p></div>',
    );
  }

}

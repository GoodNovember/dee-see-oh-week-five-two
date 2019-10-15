<?php

namespace Drupal\html5_audio\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'html5audio_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "html5audio_field_formatter",
 *   label = @Translation("HTML5 Audio"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class Html5AudioFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'autoplay' => '0'
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Play Automatically'),
      '#default_value' => $this->getSetting('autoplay')
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    $currentSettings = $this->getSettings();

    if($currentSettings['autoplay']){
      $summary[] = t('Autoplay is Enabled.');
    }else{
      $summary[] = t('Autoplay is Disabled.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    // $items represents all the data we are given.
    $elements = [];

    //Render all the field values as part of a single <audio> tag.

    $sources = [];

    foreach($items as $delta => $item){
      $mimetype = \Drupal::service('file.mime_type.guesser')->guess($item->uri);
      $sources[$delta] = [
        'src' => $item->uri,
        'mimetype' => $mimetype
      ];
    }

    //Put everything in an array for theming.

    $elements[] = [
      '#theme' => 'audio_tag',
      '#sources' => $sources,
      '#autoplay' => $this->getSetting('autoplay')
    ];

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->uri));
  }

}

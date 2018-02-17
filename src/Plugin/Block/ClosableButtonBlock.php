<?php

namespace Drupal\mailchimp_signup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'ClosableButtonBlock' block.
 *
 * @Block(
 *  id = "mailchimp_signup_closable_button",
 *  admin_label = @Translation("Mailchimp closable signup button"),
 * )
 */
class ClosableButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'mailing_list_description' => '',
      'button_label' => $this->t('Subscribe'),
      'signup_form_url' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['mailing_list_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mailing list description'),
      '#description' => $this->t('Optional text that explains the destination of the mailing list.'),
      '#default_value' => $this->configuration['mailing_list_description'],
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '1',
    ];
    $form['button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button label'),
      '#description' => $this->t('Call to action label'),
      '#default_value' => $this->configuration['button_label'],
      '#maxlength' => 80,
      '#size' => 64,
      '#required' => TRUE,
      '#weight' => '2',
    ];
    $form['signup_form_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Signup form'),
      '#description' => $this->t('The Mailchip mailing list signup form url.'),
      '#default_value' => $this->configuration['signup_form_url'],
      '#required' => TRUE,
      '#weight' => '3',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['mailing_list_description'] = $form_state->getValue('mailing_list_description');
    $this->configuration['button_label'] = $form_state->getValue('button_label');
    $this->configuration['signup_form_url'] = $form_state->getValue('signup_form_url');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $buttonUrl = Url::fromUri($this->configuration['signup_form_url']);
    $buttonLink = Link::fromTextAndUrl($this->configuration['button_label'], $buttonUrl);
    $buttonLink = $buttonLink->toRenderable();
    $buttonLink['#attributes'] = ['class' => ['button', 'btn', 'btn-primary']];
    $build = [
      'mailchimp_signup_closable_button' => [
        '#theme' => 'closable_button',
        '#mailing_list_description' => $this->configuration['mailing_list_description'],
        '#button_label' => $this->configuration['button_label'],
        '#signup_form_url' => $this->configuration['signup_form_url'],
        '#button_link' => $buttonLink,
        '#attached' => [
          'library' => [
            'mailchimp_signup/closable_button',
          ],
          'drupalSettings' => [
            // @todo pass from configuration
            'cookie_expire_days' => 10,
          ],
        ],
      ],
    ];
    return $build;
  }

}

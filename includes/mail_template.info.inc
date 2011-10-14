<?php

/**
 * @file
 * Provides entity property info for Mail Templates.
 */
class MailTemplateMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    //$properties = &$info['mail_template']['properties'];
    return $info;
  }

}
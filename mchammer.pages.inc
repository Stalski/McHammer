<?php
/*
 * @file
 * Pages for McHammer mail templates.
 */

/**
 * Page callback to render/preview a mail template.
 */
function mchammer_mail_template_page($mail_template) {
  return panels_render_display($mail_template->display);
}
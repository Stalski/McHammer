<?php
/*
 * @file
 * Pages for McHammer pages.
 */

/**
 * Page callback to render/preview a mail template.
 */
function mchammer_mail_template_page($mail_template) {
  return panels_render_display($mail_template->display);
}

/**
 * Page callback to render/preview a newsletter.
 */
function mchammer_newsletter_page($newsletter) {
  return panels_render_display($newsletter->display);
}
<?php
/**
 * View for the Grow Journey Troubleshoot Confirmation page.
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

// Prevent direct access

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

use Grow\WordPress;

$args = $args ?? [];

$main_classes = [ 'grow-admin-page', 'grow-journey', 'grow-journey__state-confirm' ];
?>

<main class="<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>">
	<div class="grow-journey__confirm-wrapper">
		<div class="grow-journey__confirm-inner">
			<div class="grow-journey__confirm-top-wrapper">
				<div class="grow-journey__confirm-top-inner">
					<div class="grow-journey__divider"></div>

					<div class="grow-journey__logo">
						<img src="<?php echo esc_url($args['all']['logo']); ?>" alt="<?php echo esc_attr( $args['all']['logo_alt'] ); ?>" />
					</div>

					<div class="grow-journey__divider"></div>
				</div>
				<h1><?php echo esc_html( $args['confirm']['header'] ); ?></h1>
				<div class="grow-journey__intro-text">
					<?php echo wp_kses( $args['confirm']['intro_text'], WordPress::wp_kses_allowed_html() ); ?>
				</div>
			</div>
			<div class="grow-journey__confirm-bottom-wrapper">
				<div class="grow-journey__buttons-wrapper">
					<button id="grow-journey-troubleshoot-decline" type="button" name="grow-journey-troubleshoot-decline" class="grow-journey__button grow-journey__button-secondary"><?php echo esc_html( $args['confirm']['button_secondary'] ); ?></button>
					<button id="grow-journey-troubleshoot-approve" type="button" name="grow-journey-troubleshoot-approve" class="grow-journey__button grow-journey__button-primary"><?php echo esc_html( $args['confirm']['button_primary'] ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="grow-journey__confirm-congrats-wrapper grow-journey__confirm-state-success">

		<div class="grow-journey__confirm-success-inner">
			<div class="grow-journey__confirm-success-top">
				<svg width="49" height="48" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M24.4997 0H19.4697C9.25973 0 0.969727 8.28 0.969727 18.5V47.06H29.5297C39.7397 47.06 48.0297 38.78 48.0297 28.56V0H24.4997ZM24.4997 32.06C24.1797 32.39 23.8697 32.72 23.5397 33.04L13.2197 43.36H13.2097L14.5497 33.47L4.66973 34.81H4.65973L14.9797 24.48C15.2997 24.16 15.6297 23.84 15.9597 23.52C18.6497 20.92 21.5097 18.54 24.4897 16.39C29.4497 12.82 34.7597 9.91 40.2397 7.77C38.1097 13.25 35.1897 18.56 31.6197 23.52C29.4697 26.51 27.0897 29.36 24.4897 32.05L24.4997 32.06Z" fill="#094352"/>
				</svg>

				<div class="grow-journey__confirm-success-text">
					<h1><?php echo esc_html( $args['success']['header'] ); ?></h1>
					<p><?php echo wp_kses( $args['success']['body'], WordPress::wp_kses_allowed_html() ); ?></p>
				</div>
			</div>
			<div class="grow-journey__buttons-wrapper">
				<button id="grow-journey-success-return" type="button" name="grow-journey-success-return" class="grow-journey__button grow-journey__button-secondary"><?php echo esc_html( $args['success']['button_return'] ); ?></button>
			</div>
		</div>
	</div>

	<div class="grow-journey__confirm-message-wrapper grow-journey__confirm-state-error">
		<div class="grow-journey__confirm-message-inner">
			<div class="grow-journey__confirm-message-top">
				<div class="grow-journey__divider"></div>
				<div class="grow-journey__confirm-message-icon">
					<img src="<?php echo esc_url($args['error']['icon']); ?>" alt="<?php echo esc_attr( $args['error']['icon_alt'] ); ?>" />
				</div>
				<div class="grow-journey__divider"></div>
			</div>

			<div class="grow-journey__confirm-message-bottom">
				<div class="grow-journey__confirm-message-text">
					<h1><?php echo esc_html( $args['error']['header'] ); ?></h1>
					<p><?php echo esc_html( $args['error']['body'] ); ?></p>
				</div>

				<div class="grow-journey__buttons-wrapper">
					<button id="grow-journey-error-cancel" type="button" name="grow-journey-error-cancel" class="grow-journey__button grow-journey__button-secondary"><?php echo esc_html( $args['error']['button_cancel'] ); ?></button>
					<button id="grow-journey-error-retry" type="button" name="grow-journey-error-retry" class="grow-journey__button grow-journey__button-primary"><?php echo esc_html( $args['error']['button_retry'] ); ?></button>
				</div>
			</div>
		</div>
	</div>
</main>

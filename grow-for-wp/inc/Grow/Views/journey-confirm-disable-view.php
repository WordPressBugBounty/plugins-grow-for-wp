<?php
/**
 * View for the Grow Journey Disable Confirmation page.
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

// Prevent direct access

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

use Grow\WordPress;

$args = $args ?? [];

$main_classes = [ 'grow-admin-page', 'grow-journey', 'grow-journey-disable', 'grow-journey__state-confirm' ];
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
			</div>
			<div class="grow-journey__confirm-bottom-wrapper">
				<div class="grow-journey__permissions-wrapper">
					<div class="grow-journey__permissions-inner">
						<div class="grow-journey__confirm-content-wrapper">
							<div class="grow-journey__confirm-error-icon">
								<img src="<?php echo esc_url($args['confirm']['icon']); ?>" alt="<?php echo esc_attr( $args['confirm']['icon_alt'] ); ?>" />
							</div>
							<div class="grow-journey__confirm-success-text">
								<h1><?php echo esc_html( $args['confirm']['header'] ); ?></h1>
								<p><?php echo esc_html( $args['confirm']['body'] ); ?></p>
							</div>
						</div>
						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13.4217 12.4016L17.7217 8.11155C18.1138 7.71943 18.1138 7.08367 17.7217 6.69155C17.3295 6.29943 16.6938 6.29943 16.3017 6.69155L12.0117 10.9916L7.72166 6.69155C7.32954 6.29943 6.69378 6.29943 6.30166 6.69155C5.90954 7.08367 5.90954 7.71943 6.30166 8.11155L10.6017 12.4016L6.30166 16.6916C6.11235 16.8793 6.00586 17.1349 6.00586 17.4016C6.00586 17.6682 6.11235 17.9238 6.30166 18.1116C6.48942 18.3009 6.74502 18.4074 7.01166 18.4074C7.2783 18.4074 7.53389 18.3009 7.72166 18.1116L12.0117 13.8116L16.3017 18.1116C16.4894 18.3009 16.745 18.4074 17.0117 18.4074C17.2783 18.4074 17.5339 18.3009 17.7217 18.1116C17.911 17.9238 18.0175 17.6682 18.0175 17.4016C18.0175 17.1349 17.911 16.8793 17.7217 16.6916L13.4217 12.4016Z" fill="#212B36"/>
							</svg>
							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_1'] ); ?></div>
						</div>
						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13.4217 12.4016L17.7217 8.11155C18.1138 7.71943 18.1138 7.08367 17.7217 6.69155C17.3295 6.29943 16.6938 6.29943 16.3017 6.69155L12.0117 10.9916L7.72166 6.69155C7.32954 6.29943 6.69378 6.29943 6.30166 6.69155C5.90954 7.08367 5.90954 7.71943 6.30166 8.11155L10.6017 12.4016L6.30166 16.6916C6.11235 16.8793 6.00586 17.1349 6.00586 17.4016C6.00586 17.6682 6.11235 17.9238 6.30166 18.1116C6.48942 18.3009 6.74502 18.4074 7.01166 18.4074C7.2783 18.4074 7.53389 18.3009 7.72166 18.1116L12.0117 13.8116L16.3017 18.1116C16.4894 18.3009 16.745 18.4074 17.0117 18.4074C17.2783 18.4074 17.5339 18.3009 17.7217 18.1116C17.911 17.9238 18.0175 17.6682 18.0175 17.4016C18.0175 17.1349 17.911 16.8793 17.7217 16.6916L13.4217 12.4016Z" fill="#212B36"/>
							</svg>
							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_2'] ); ?></div>
						</div>
						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13.4217 12.4016L17.7217 8.11155C18.1138 7.71943 18.1138 7.08367 17.7217 6.69155C17.3295 6.29943 16.6938 6.29943 16.3017 6.69155L12.0117 10.9916L7.72166 6.69155C7.32954 6.29943 6.69378 6.29943 6.30166 6.69155C5.90954 7.08367 5.90954 7.71943 6.30166 8.11155L10.6017 12.4016L6.30166 16.6916C6.11235 16.8793 6.00586 17.1349 6.00586 17.4016C6.00586 17.6682 6.11235 17.9238 6.30166 18.1116C6.48942 18.3009 6.74502 18.4074 7.01166 18.4074C7.2783 18.4074 7.53389 18.3009 7.72166 18.1116L12.0117 13.8116L16.3017 18.1116C16.4894 18.3009 16.745 18.4074 17.0117 18.4074C17.2783 18.4074 17.5339 18.3009 17.7217 18.1116C17.911 17.9238 18.0175 17.6682 18.0175 17.4016C18.0175 17.1349 17.911 16.8793 17.7217 16.6916L13.4217 12.4016Z" fill="#212B36"/>
							</svg>
							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_3'] ); ?></div>
						</div>
					</div>
				</div>
				<div class="grow-journey__buttons-wrapper">
					<button id="grow-journey-disable-decline" type="button" name="grow-journey-disable-decline" class="grow-journey__button grow-journey__button-secondary"><?php echo esc_html( $args['confirm']['button_secondary'] ); ?></button>
					<button id="grow-journey-disable-approve" type="button" name="grow-journey-disable-approve" class="grow-journey__button grow-journey__button-primary grow-journey__button-warning"><?php echo esc_html( $args['confirm']['button_primary'] ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="grow-journey__confirm-message-wrapper grow-journey__confirm-state-success">
		<div class="grow-journey__confirm-message-inner">
			<div class="grow-journey__confirm-message-top">
				<div class="grow-journey__divider"></div>
				<div class="grow-journey__confirm-message-icon">
					<img src="<?php echo esc_url($args['success']['icon']); ?>" alt="<?php echo esc_attr( $args['success']['icon_alt'] ); ?>" />
				</div>
				<div class="grow-journey__divider"></div>
			</div>

			<div class="grow-journey__confirm-message-bottom">
				<div class="grow-journey__confirm-message-text">
					<h1><?php echo esc_html( $args['success']['header'] ); ?></h1>
					<p><?php echo esc_html( $args['success']['body'] ); ?></p>
				</div>

				<div class="grow-journey__buttons-wrapper">
					<button id="grow-journey-success-return" type="button" name="grow-journey-success-return" class="grow-journey__button grow-journey__button-secondary grow-journey__button-full"><?php echo esc_html( $args['success']['button_return'] ); ?></button>
				</div>
			</div>
		</div>
	</div>

</main>

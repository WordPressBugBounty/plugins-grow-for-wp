<?php
/**
 * View for the Grow Journey Enable Confirmation page.
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
				<div class="grow-journey__intro-text">
					<?php echo wp_kses( $args['confirm']['intro_text'], WordPress::wp_kses_allowed_html() ); ?>
				</div>
			</div>
			<div class="grow-journey__confirm-bottom-wrapper">
				<div class="grow-journey__permissions-wrapper">
					<div class="grow-journey__permissions-inner">
						<div class="grow-journey__icon-auth">
							<svg width="69" height="68" viewBox="0 0 69 68" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g opacity="0.98">
									<rect x="1.5" y="1" width="66" height="66" rx="33" stroke="white" stroke-width="2"/>
									<rect x="2.5" y="2" width="64" height="64" rx="32" fill="#EEF5FF"/>
									<path opacity="0.48" d="M27.1812 20.5591C28.7211 18.5886 31.196 17.3644 33.7034 17.4043C36.2093 17.3644 38.6798 18.5857 40.2211 20.5518C41.4526 22.096 42.1031 24.0931 42.0013 26.0665C40.9644 25.832 39.9113 25.6816 38.8627 25.5105C38.8332 23.9087 37.9659 22.3615 36.6503 21.4588C35.4099 20.5842 33.7727 20.2936 32.3125 20.7214C30.1989 21.2863 28.575 23.3158 28.5441 25.5134C27.4954 25.6801 26.4423 25.8305 25.4069 26.068C25.3051 24.096 25.9526 22.1019 27.1812 20.5591Z" fill="#694DE3"/>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M46.1689 30.0274C46.1678 29.62 46.1667 29.2127 46.1693 28.8055C38.1737 26.04 29.2549 26.0356 21.2578 28.7996C21.2597 29.3723 21.2592 29.9453 21.2586 30.5183C21.2579 31.2823 21.2572 32.0463 21.2622 32.8099C21.3906 35.6639 22.1133 38.4958 23.4171 41.04C25.5616 45.3217 29.2917 48.7642 33.7032 50.6138C36.8006 49.3099 39.569 47.2332 41.6782 44.6167C44.4997 41.1433 46.1192 36.7067 46.1531 32.2288C46.173 31.4949 46.171 30.761 46.1689 30.0274ZM32.4024 36.8085C31.2829 36.3011 30.5203 35.0858 30.6221 33.8483C30.6354 32.2568 32.1059 30.8527 33.6988 30.9161C35.3478 30.8424 36.8611 32.3483 36.7858 33.9987C36.8345 35.1846 36.0749 36.3203 35.0056 36.807C36.0602 37.1772 36.9717 37.953 37.4422 38.9722C37.8006 39.6742 37.8463 40.4766 37.8611 41.2495C35.0897 41.2509 32.3183 41.2509 29.5454 41.2495C29.5336 40.2214 29.7136 39.1492 30.3566 38.3144C30.8522 37.6064 31.5941 37.102 32.4024 36.8085Z" fill="#694DE3"/>
								</g>
							</svg>
						</div>

						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g id="icon/ic_checkmark">
									<path id="Path" d="M10.3601 17.9999C10.0829 17.999 9.81856 17.8832 9.63009 17.6799L4.77009 12.5099C4.39177 12.1068 4.41192 11.4733 4.81509 11.0949C5.21826 10.7166 5.85177 10.7368 6.23009 11.1399L10.3501 15.5299L18.7601 6.32994C18.9926 6.04035 19.3665 5.90351 19.7311 5.97455C20.0956 6.04559 20.3908 6.31281 20.4976 6.66853C20.6044 7.02424 20.5052 7.40985 20.2401 7.66994L11.1001 17.6699C10.9134 17.8769 10.6488 17.9965 10.3701 17.9999H10.3601Z" fill="#212B36"/>
								</g>
							</svg>

							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_1'] ); ?></div>
						</div>
						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g id="icon/ic_checkmark">
									<path id="Path" d="M10.3601 17.9999C10.0829 17.999 9.81856 17.8832 9.63009 17.6799L4.77009 12.5099C4.39177 12.1068 4.41192 11.4733 4.81509 11.0949C5.21826 10.7166 5.85177 10.7368 6.23009 11.1399L10.3501 15.5299L18.7601 6.32994C18.9926 6.04035 19.3665 5.90351 19.7311 5.97455C20.0956 6.04559 20.3908 6.31281 20.4976 6.66853C20.6044 7.02424 20.5052 7.40985 20.2401 7.66994L11.1001 17.6699C10.9134 17.8769 10.6488 17.9965 10.3701 17.9999H10.3601Z" fill="#212B36"/>
								</g>
							</svg>

							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_2'] ); ?></div>
						</div>
						<div class="grow-journey__permissions-checkbox">
							<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g id="icon/ic_checkmark">
									<path id="Path" d="M10.3601 17.9999C10.0829 17.999 9.81856 17.8832 9.63009 17.6799L4.77009 12.5099C4.39177 12.1068 4.41192 11.4733 4.81509 11.0949C5.21826 10.7166 5.85177 10.7368 6.23009 11.1399L10.3501 15.5299L18.7601 6.32994C18.9926 6.04035 19.3665 5.90351 19.7311 5.97455C20.0956 6.04559 20.3908 6.31281 20.4976 6.66853C20.6044 7.02424 20.5052 7.40985 20.2401 7.66994L11.1001 17.6699C10.9134 17.8769 10.6488 17.9965 10.3701 17.9999H10.3601Z" fill="#212B36"/>
								</g>
							</svg>

							<div class="grow-journey__permissions-checkbox--text"><?php echo esc_html( $args['confirm']['check_3'] ); ?></div>
						</div>
					</div>
				</div>
				<div class="grow-journey__buttons-wrapper">
					<button id="grow-journey-enable-decline" type="button" name="grow-journey-enable-decline" class="grow-journey__button grow-journey__button-secondary"><?php echo esc_html( $args['confirm']['button_secondary'] ); ?></button>
					<button id="grow-journey-enable-approve" type="button" name="grow-journey-enable-approve" class="grow-journey__button grow-journey__button-primary"><?php echo esc_html( $args['confirm']['button_primary'] ); ?></button>
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

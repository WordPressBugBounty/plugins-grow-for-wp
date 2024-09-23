<?php
/**
 * View for the Grow Admin Page
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

use Grow\WordPress;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

$args = $args ?? [];

$main_classes = [ 'grow-admin-page' ];
if ( $args['is_connected'] ) {
	$main_classes[] = 'grow-admin-page--is-connected';
}
?>

<main class="<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>">
	<section class="grow-admin-page__header">
		<svg
				viewBox="0 0 569.83 152.86"
				aria-hidden="true"
				focusable="false"
				role="img"
				class="grow-admin-page__logo"
		>
			<title>Grow</title>
			<path d="M246.84 117.08a34.72 34.72 0 0 1-31.29-18.61 40.32 40.32 0 0 1-4.65-19.38 39.49 39.49 0 0 1 4.65-19.24 35 35 0 0 1 31.29-18.44 33.8 33.8 0 0 1 15 3.23 24.86 24.86 0 0 1 10.32 9.07 26.85 26.85 0 0 1 4 13.55v24a25.39 25.39 0 0 1-14.42 22.54 32.54 32.54 0 0 1-14.9 3.28Zm.95 35.78a50.1 50.1 0 0 1-21.52-4.34 37.86 37.86 0 0 1-14.9-12.21l13.24-13.09a31.75 31.75 0 0 0 9.86 8 28.79 28.79 0 0 0 13.16 2.76q9.45 0 15.13-5t5.68-13.32V96.43l3.31-16.71L268.59 63V43h20.5v72.35a36.64 36.64 0 0 1-5.28 19.71 35.5 35.5 0 0 1-14.59 13.16 47.3 47.3 0 0 1-21.43 4.64Zm3-54.7a20 20 0 0 0 10-2.37 16.15 16.15 0 0 0 6.46-6.69 22.35 22.35 0 0 0 0-19.71 16.55 16.55 0 0 0-6.54-6.78 20.94 20.94 0 0 0-19.62 0 17.75 17.75 0 0 0-6.62 6.78 19.39 19.39 0 0 0-2.45 9.7 20.21 20.21 0 0 0 2.37 9.85 16.94 16.94 0 0 0 6.7 6.78 19.38 19.38 0 0 0 9.69 2.44ZM307.21 119.12V43h20.65v76.13Zm20.65-41.93-8-5.51q1.43-14 8.2-22.15t19.55-8.12a26 26 0 0 1 10 1.81 22.08 22.08 0 0 1 8 5.91l-12.93 15a9.71 9.71 0 0 0-4.18-2.92 16.87 16.87 0 0 0-5.59-.86 14.72 14.72 0 0 0-10.8 4.1q-4.25 4.07-4.25 12.74ZM406.37 120.86a40.93 40.93 0 0 1-20.73-5.28 39.2 39.2 0 0 1-19.94-34.76 38.17 38.17 0 0 1 5.36-20.1 39.86 39.86 0 0 1 14.58-14.19 40.93 40.93 0 0 1 20.73-5.28 41.75 41.75 0 0 1 20.88 5.21 39.43 39.43 0 0 1 14.66 14.18 38.17 38.17 0 0 1 5.44 20.18 39.47 39.47 0 0 1-5.35 20.33 39 39 0 0 1-14.66 14.43 41.71 41.71 0 0 1-20.97 5.28Zm0-19.39a19.72 19.72 0 0 0 10.32-2.68 18.39 18.39 0 0 0 6.94-7.25A22.18 22.18 0 0 0 426.07 81a21 21 0 0 0-2.6-10.56 20.16 20.16 0 0 0-27.19-7.18 18 18 0 0 0-6.86 7.18A22 22 0 0 0 387 81a22.06 22.06 0 0 0 2.44 10.56 18.54 18.54 0 0 0 6.86 7.25 19 19 0 0 0 10.07 2.66ZM478.88 119.12 450.5 43h20.81l18.44 55.33-6.15.15L502.68 43h14.82l19.23 55.48-6.15-.15L549 43h20.81l-28.37 76.13h-15l-19.03-52.5h5.83l-19.39 52.49Z"/>
			<path d="M70.33 135.2A131.69 131.69 0 0 1 52.46 116c-.52-.68-1-1.38-1.54-2.08-.81-1.14-1.63-2.33-2.42-3.53q-.23-.33-.45-.69c-.35-.53-.67-1-1-1.49-.66-1.07-1.32-2.17-1.95-3.27q-1.17-2-2.25-4.16a74.88 74.88 0 0 1-7.6-23.48c-.13-.89-.23-1.79-.32-2.68s-.16-1.93-.22-3-.07-1.9-.07-2.81v-.47c0-.78 0-1.59.06-2.5a68.11 68.11 0 0 1 4.33-20.69c.32-.84.62-1.61.92-2.34s.64-1.53 1-2.28.7-1.53 1.05-2.27.73-1.49 1.1-2.23c.85-1.64 1.77-3.28 2.74-4.92a119.4 119.4 0 0 0-31.37-6l-7.21-.21L5 31.79a107.59 107.59 0 0 0-3.11 13.05c-.28 1.64-.56 3.43-.82 5.32a135.634 135.634 0 0 0-.53 4.51v.25a100.45 100.45 0 0 0 .32 23.35c.11.74.22 1.49.35 2.23s.26 1.47.4 2.21.31 1.46.48 2.19.34 1.45.54 2.17a59.49 59.49 0 0 0 7.37 17c.39.6.79 1.2 1.21 1.8l.17.23c.49.69 1 1.37 1.52 2s1.08 1.34 1.64 2 1 1.18 1.57 1.75A67 67 0 0 0 33.72 125c1.27.67 2.56 1.3 3.84 1.9 1.07.5 2.14 1 3.2 1.41l1.51.62.72.28c1.24.48 2.47.94 3.7 1.36l2.28.76a123.58 123.58 0 0 0 22.5 4.84Z" fill="#e66438"/>
			<path d="M92.27 4.34 86.51 0l-5.78 4.3a107.2 107.2 0 0 0-10.09 8.85c-1.18 1.17-2.44 2.47-3.75 3.87l-1.52 1.66c-.5.55-1 1.13-1.51 1.71l-.17.2c-1.75 2-3.52 4.22-5.25 6.56-.83 1.12-1.64 2.28-2.45 3.47s-1.53 2.31-2.27 3.51a81.1 81.1 0 0 0-3.24 5.71c-.34.67-.68 1.34-1 2s-.64 1.35-.95 2-.59 1.36-.88 2.06-.55 1.38-.81 2.09A59.21 59.21 0 0 0 43 66.2c0 .72-.05 1.44-.06 2.17v.29c0 .85 0 1.69.07 2.53s.11 1.72.19 2.57.17 1.56.27 2.34A67 67 0 0 0 50.29 97c.66 1.28 1.34 2.54 2 3.77q.89 1.53 1.8 3c.29.47.58.92.88 1.38.13.22.27.43.41.64.74 1.11 1.49 2.2 2.24 3.25.47.66 1 1.3 1.42 1.93a123.87 123.87 0 0 0 15.57 17q-.16-.69-.36-1.47a132.59 132.59 0 0 1-3.47-26v-2.59c0-1.4 0-2.84.06-4.28v-.82c0-.64 0-1.22.08-1.77.07-1.26.17-2.54.29-3.8.15-1.54.34-3.12.57-4.69a75.3 75.3 0 0 1 7.36-23.57c.41-.79.85-1.59 1.29-2.36.28-.49.58-1 .9-1.51L82 54c.53-.83 1-1.59 1.57-2.34l.08-.13.13-.19v-.07c.45-.64.94-1.29 1.49-2 .35-.45.71-.88 1.07-1.32a68.75 68.75 0 0 1 14.42-13.07c.75-.5 1.44-1 2.11-1.38s1.41-.88 2.1-1.3 1.49-.87 2.17-1.24 1.45-.79 2.19-1.17c1.64-.86 3.34-1.67 5.08-2.44A119.4 119.4 0 0 0 92.27 4.34Z" fill="#feb800"/>
			<path d="m167.8 32.3-2.19-6.86-7.21.17A105.79 105.79 0 0 0 145.06 27c-1.64.28-3.43.61-5.3 1l-2.19.48c-.74.16-1.48.34-2.23.52l-.25.07c-2.6.64-5.31 1.41-8.07 2.31-1.33.44-2.67.91-4 1.42s-2.58 1-3.88 1.55c-2 .85-4 1.78-5.94 2.8-.66.34-1.32.7-2 1.07s-1.3.73-2 1.12-1.27.77-1.91 1.17-1.25.81-1.87 1.23a59.87 59.87 0 0 0-13.56 12.6l-.51.7c-.26.34-.53.69-.78 1l-.16.24c-.49.7-1 1.4-1.41 2.11l-.26.42c-.37.59-.73 1.19-1.07 1.79s-.77 1.4-1.13 2.1l-.22.45A67.35 67.35 0 0 0 80 83.67q-.31 2.13-.51 4.26-.18 1.75-.27 3.48c0 .55-.06 1.09-.08 1.63v.77c0 1.33-.06 2.65 0 4v2.4a122.92 122.92 0 0 0 3.26 24.37c.72 3 1.33 5 1.49 5.56v.1l.5 1.59.44 1.36.31 1 .55 1.74.11.35.07.23.07.21.14.43h7.15c.18 0 .75 0 1.63-.07 1.29-.07 3.27-.22 5.78-.5 1.75-.2 3.74-.46 5.94-.82 15.48-2.51 40.59-9.53 54.51-29.07 20.73-29.29 7.3-72.56 6.71-74.39Z" fill="#4b8df0"/>
		</svg>
		<div class="grow-admin-page__text">
			<p class="grow-admin-page__connected-site"><?php echo esc_html( $args['connected_site'] ); ?></p>
			<h1 class="grow-admin-page__title"><?php echo esc_html( $args['title'] ); ?></h1>
			<p class="grow-admin-page__subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
		</div>
		<div class="grow-admin-page__actions">
			<a class="grow-admin-page__primary-button" id="grow-remote-connect" target="_blank" href="<?php echo esc_url( $args['primary_button_href'] ); ?>"><?php echo esc_html( $args['primary_button_text'] ); ?></a>
			<a class="grow-admin-page__secondary-button" id="grow-remote-sign-out" href="<?php echo esc_url( $args['secondary_button_href'] ); ?>"><span><?php echo esc_html( $args['secondary_button_text'] ); ?></span>
				<svg width="15" height="14" viewBox="0 0 15 14" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
					<path d="M11.25 4L10.1925 5.0575L11.3775 6.25H5.25V7.75H11.3775L10.1925 8.935L11.25 10L14.25 7L11.25 4ZM2.25 1.75H7.5V0.25H2.25C1.425 0.25 0.75 0.925 0.75 1.75V12.25C0.75 13.075 1.425 13.75 2.25 13.75H7.5V12.25H2.25V1.75Z" fill="#4B8DF0"/>
				</svg>
			</a>
		</div>
	</section>
	<?php
	if ( ! empty( $args['features'] ) ) {
		?>
		<section class="grow-admin-page__features">
			<?php
			foreach ( $args['features'] as $feature ) {
				?>
				<a href="<?php echo esc_url( $feature['help_link'] ); ?>" tabindex="0" target="_blank" class="grow-admin-page__feature grow-admin-page__feature--<?php echo esc_attr( $feature['slug'] ); ?>">
					<img alt="" src="<?php echo esc_url( $feature['image_source'] ); ?>"/>
					<h2><?php echo esc_html( $feature['title'] ); ?></h2>
					<p><?php echo esc_html( $feature['description'] ); ?></p>
				</a>
				<?php
			}
			?>
		</section>
		<?php
	}
	if ( ! empty( $args['integrations'] ) ) {
		?>
		<button type="button" aria-expanded="true" class="collapse-button" aria-controls="integrations-section" id="integrations-section-button">
			<span class="collapse-button__title">
				Third Party Integrations
				<span class="collapse-button__icon">
				<svg viewBox="0 0 16 9" xmlns="http://www.w3.org/2000/svg">
					<path d="M0 9 l8 -9 l8 9"/>
				</svg>
				</span>
			</span>
		</button>
		<section class="grow-admin-page__features grow-admin-page__features--integrations grow-admin-page__collapsable" id="integrations-section" role="region" aria-labelledby="integrations-section" aria-hidden="false">
			<?php
			foreach ( $args['integrations'] as $feature ) {
				?>
				<a href="<?php echo esc_url( $feature['help_link'] ); ?>" target="_blank" class="grow-admin-page__feature grow-admin-page__feature--<?php echo esc_attr( $feature['slug'] ); ?>">
					<img alt="" src="<?php echo esc_url( $feature['image_source'] ); ?>"/>
					<h2><?php echo esc_html( $feature['title'] ); ?></h2>
					<p><?php echo esc_html( $feature['description'] ); ?></p>
				</a>
				<?php
			}
			?>
		</section>
	<?php } ?>
</main>

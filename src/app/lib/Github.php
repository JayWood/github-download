<?php

namespace com\plugish\plugins\githubdownloads\app\lib;

class GitHub {

	const ApiBase = 'https://api.github.com/';

	public function __construct(
		public string $token,
		public string $url_base
	) {}

	private function get_headers(): array {
		return [
			'Accept' => 'application/vnd.github.v3+json',
			'Authorization' => 'token ' . $this->token,
		];
	}

	public function get_url_base(): string {
		return 'WooMinecraft/pro-java';
	}

	public function get_releases(): ?array {
		$cache = get_transient( 'github_releases' );
		if ( false !== $cache ) {
			return json_decode( $cache, true );
		}

		$request = wp_remote_get(
			self::ApiBase . 'repos/' . $this->get_url_base() . '/releases',
			[
				'headers' => $this->get_headers(),
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $request );
		$decoded = json_decode( $body, true );
		if ( empty( $decoded ) ) {
			return [];
		}

		set_transient( 'github_releases', $body, 30 * MINUTE_IN_SECONDS );
		return $decoded;
	}

	/**
	 * @param string $asset_url
	 *
	 * @return array
	 */
	public function list_release_assets_from_url( string $asset_url ): array {
		$request = wp_remote_get(
			$asset_url,
			[
				'headers' => $this->get_headers(),
			]
		);

		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $request );
		$decoded = json_decode( $body, true );
		if ( empty( $decoded ) ) {
			return [];
		}

		return $decoded;
	}

	/**
	 * Downloads an asset to /tmp and returns the filename for usage.
	 *
	 * @param int $asset_id
	 *
	 * @return string
	 */
	public function download_asset( int $asset_id ): string {
		$url = $this->get_url_base() . '/releases/assets/' . $asset_id;
		$tmpfname = wp_tempnam( md5( $url ) );
		if ( ! $tmpfname ) {
			return '';
		}

		$headers = $this->get_headers();
		$headers['Accept'] = 'application/octet-stream';

		$request = wp_remote_get(
			$url,
			[
				'headers' => $headers,
				'timeout' => 300,
				'stream' => true,
				'filename' => $tmpfname,
			],
		);

		if ( is_wp_error( $request ) ) {
			unlink( $tmpfname );
			return '';
		}
		$code = wp_remote_retrieve_response_code( $request );
		if ( ! in_array( $code, [ 200, 302 ] ) ) {
			return '';
		}

		return $tmpfname;
	}
}
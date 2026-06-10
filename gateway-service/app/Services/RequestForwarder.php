<?php

namespace App\Services;

/**
 * Simple HTTP forwarder using cURL.
 */
class RequestForwarder
{
	/**
	 * Forward a request to a target URL using cURL.
	 * @param string $method
	 * @param string $url
	 * @param array $headers Array of header strings like "Key: value" or associative array
	 * @param mixed $body Raw body (string) or array which will be JSON-encoded
	 * @return array ['body' => string, 'status' => int]
	 */
	public function forward(string $method, string $url, array $headers = [], $body = null): array
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Capture response headers so gateway can propagate Set-Cookie and others
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

		// Normalize headers to array of "Key: value"
		$outHeaders = [];
		foreach ($headers as $k => $v) {
			if (is_int($k)) {
				$outHeaders[] = $v;
			} else {
				$outHeaders[] = $k . ': ' . $v;
			}
		}

		if (!empty($outHeaders)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $outHeaders);
		}

		if ($body !== null) {
			if (is_array($body) || is_object($body)) {
				$payload = json_encode($body);
				// Ensure Content-Type JSON if not provided
				$hasCt = false;
				foreach ($outHeaders as $h) {
					if (stripos($h, 'content-type:') === 0) {
						$hasCt = true;
						break;
					}
				}
				if (!$hasCt) {
					$outHeaders[] = 'Content-Type: application/json';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $outHeaders);
				}
			} else {
				$payload = $body;
			}

			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		}

		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;

		$headerSize = 0;
		$headers = [];
		$bodyContent = '';

		if ($response !== false) {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$rawHeader = substr($response, 0, $headerSize);
			$bodyContent = substr($response, $headerSize);

			// Parse raw headers into associative array preserving duplicates (e.g., Set-Cookie)
			$lines = preg_split("/\r\n|\n|\r/", trim($rawHeader));
			foreach ($lines as $line) {
				if (strpos($line, ':') !== false) {
					list($name, $value) = explode(':', $line, 2);
					$name = trim($name);
					$value = trim($value);
					if (!isset($headers[$name])) $headers[$name] = [];
					$headers[$name][] = $value;
				}
			}
		} else {
			$bodyContent = '';
		}

		curl_close($ch);

		return ['body' => $bodyContent, 'status' => (int)$status, 'headers' => $headers];
	}
}


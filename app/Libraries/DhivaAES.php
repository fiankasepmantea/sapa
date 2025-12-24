<?php

namespace Dhiva\Core;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DhivaAES
{
	public static function base64url_encode($data, $password = false)
	{
		return rtrim(strtr(base64_encode(openssl_encrypt($data, "AES-256-CBC", self::keypair($password), OPENSSL_RAW_DATA, Keys::$iv)), '+/', '-_'), '=');
	}
	public static function base64url_decode($data, $password = false)
	{
		$decrypt = openssl_decrypt(base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)), "AES-256-CBC", self::keypair($password), OPENSSL_RAW_DATA, Keys::$iv);
		return $decrypt;
	}
	private static function keypair($password)
	{
		$data = ($password == false) ? Keys::$password : $password;
		return openssl_pbkdf2($data, Keys::$salt, Keys::$keyLength, Keys::$iterations, "sha256");
	}
	public static function randomString($aes = false)
	{
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < 10; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}
	public static function jwtencode($token)
	{
		$tokenParts = explode(".", $token);
		$tokenPayload = self::base64url_encode($tokenParts[1]);
		return $tokenPayload;
	}
	public static function jwtdecode($private, $public)
	{
		$tokenParts = explode(".", $public);
		$tokenHeader = $tokenParts[0];
		$tokenPayload = self::base64url_decode($private);
		if ($tokenParts[2]) {
			$tokenSignature = $tokenParts[2];
			$data = $tokenHeader . "." . $tokenPayload . "." . $tokenSignature;
		} else {
			return ERROR_TOKEN_UNIDENTIFIED;
		}
		return $data;
	}
	public static function jwtvalidator($private, $public)
	{
		$tokenPrivate = self::jwtdecode($private, $public);
		$match = ($tokenPrivate === $public) ? true : false;
		return $match;
	}

	/**
	 * Generator String dengan enkripsi
	 *
	 * @param  mixed $length
	 * @param  mixed $aes
	 * @return string
	 */
	public static function randomStr(int $length, $aes = false): string
	{
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}
		if ($aes) {
			$randomString = self::base64url_encode(strval(intval(microtime(true) * 1000)), 5);
		}
		return $randomString;
	}
	public static function aesencodeid(string $string, int $length)
	{
		$tokenPayload = self::base64url_encode($string . self::randomStr($length));
		return $tokenPayload;
	}
	public static function validateTimestampWtihUserAccess($tokens)
	{
		$token = self::validateToken($tokens);
		if ($token == false) {
			return ERROR_TOKEN_UNIDENTIFIED;
		}
		$model = model('App\Models\SqlModel\SuperUserModelSql');
		$user = $model->showBy('super_user_id', $token->super_user_id);
		$lastTimeStamp = strtotime($user->access_at);
		$timeStampTimeOut = ($lastTimeStamp + JWT_TIMEOUT) - now();
		if ($timeStampTimeOut < 0 || !$user->token || $token->token != $user->token) {
			$update = ['token' => null];
			$model->update($update, $token->super_user_id);
			return ERROR_TOKEN_EXPIRED;
		}
		return $token;
	}
	public static function validateToken($token)
	{
		return JWT::decode($token, new Key(Keys::$JWT_KEY, 'HS256'));
	}
	public static function generateToken($data)
	{
		return JWT::encode($data, Keys::$JWT_KEY, 'HS256');
	}
}

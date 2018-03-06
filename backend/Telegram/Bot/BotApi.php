<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Telegram\Bot;

use Telegram\Bot\Objects\Message;

/**
 * Description of BotApi
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class BotApi extends Api
{
	/**
     * Answers callback message.
     *
     * <code>
     * $params = [
     *   'callback_query_id'	=> '',
     *   'text'					=> '',
     *   'url'					=> '',
     *   'show_alert'			=> '',
     *   'cache_time'			=> '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#answercallbackquery
     *
     * @param array    $params
     *
     * @var int|string $params ['callback_query_id']
     * @var string     $params ['text']
     * @var string     $params ['url']
     * @var bool       $params ['show_alert']
     * @var int        $params ['cache_time']
     *
     * @return Message
     */
	public function answerCallbackQuery(array $params) : Message
	{
		$response = $this->post('answerCallbackQuery', $params);

        return new Message($response->getDecodedBody());
	}
}

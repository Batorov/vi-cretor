<?php
function randomFloat($min = 0, $max = 1) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

function choices( $values, $weights )
{
    $total = array_sum( $weights );
    $n = 0;
    $num = randomFloat( 0, $total );
    foreach ( $values as $i => $value )
    {
        $n += $weights[$i];
        if ( $n >= $num )
        {
            return $values[$i];
        }
    }
}

$TOKEN = 'd16300d80c3feeed616d9c1540ac50888bebf8d98b60890adee8ceb506473476813826b24bbddc6af7638';
$confirmation_token = 'b1213944';

function answer($message)
{
  if ($message=='Здравствуйте!') {
    $text = array('Здравствуйте!', 'Меня зовут Николай. Чем я могу помочь?');
    return $text;
  } else if ($message=='Мне нужен сыр.') {
    $text = choices(array(array('Какой вид?', 'Твердые, мягкие или рассольные?'),array('Могу порекомендовать вам пармезан.', 'У него сладкий вкус и 32% жира.')),array(0.8,0.2));
    return $text;
  } else if ($message=='Мягкие.') {
    $text = choices(array(array('Могу порекомендовать вам бри.', 'У него сладкий вкус и 45% жира.'),array('Могу порекомендовать вам камамбер.', 'У него острый вкус и 45% жира.'),'Могу порекомендовать вам рокфор'),array(0.5,0.5,0.5));
    return $text;
  } else if ($message=='Твердые.') {
    $text = 'Могу порекомендовать вам чеддер.';
    return $text;
  }

}

function vk_msg_send($peer_id,$text){
  global $TOKEN;
	$request_params = array(
		'message' => is_array($text)?implode("\n",$text):$text,
		'peer_id' => $peer_id, 
		'access_token' => $TOKEN, 
		'v' => '5.110', 
    'random_id' => '0'
	);
	$get_params = http_build_query($request_params);
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
}

$data = json_decode(file_get_contents('php://input'));

switch ($data->type) {
	case 'confirmation':
		echo $confirmation_token;
	break;  

	case 'message_new':
		$message_text = $data -> object -> message -> text;
		$chat_id = $data -> object -> message -> peer_id;
		vk_msg_send($chat_id, answer($message_text));

		echo 'ok';
	break;
}
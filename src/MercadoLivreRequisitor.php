<?php 
namespace ulissesvaquero\mercadolivrerequisitor;

class MercadoLivreRequisitor extends Meli
{
	public function __construct($client_id,$client_secret,$access_token,$refresh_token) 
	{
	    $this->client_id = $client_id;
	    $this->client_secret = $client_secret;
	    $this->access_token = $access_token;
	    $this->refresh_token = $refresh_token;
		
		
		$date = new \DateTime();
		
		/*
		$date->modify('-2 hours');
		*/
		
		if($this->usuarioTokenSite->expires_at < $date->getTimestamp())
		{
			$this->getToken();
		}
	}
	
	/**
	 * ID com MLB
	 * @param unknown $id
	 */
	public function getAnuncioById($id)
	{
		$res = $this->get('/items/'.$id,['access_token' => $this->usuarioTokenSite->token]);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		return false;
	}
	
	/**
	 * Retorna a listsa de vendas vinculadas a conta cadastrada.
	 * @return mixed|boolean
	 */
	public function getTodasVendas()
	{
		//Obtenho informações da conta
		$sobreMim = $this->sobreMim();
		
		$res = $this->get('/orders/search',['access_token' => $this->usuarioTokenSite->token,'seller' => $sobreMim['body']->id]);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		
		return false;
	}
	
	
	
	public function getPerguntas($offset = 0)
	{
	    $res = $this->get('/my/received_questions/search',['access_token' => $this->usuarioTokenSite->token,'offset' => $offset]);
	    if($res['httpCode'] == 200)
	    {
	        return $res['body'];
	    }
	    
	    return false;
	}
	
	
	
	/**
	 * Retorna a listsa de vendas vinculadas a conta cadastrada.
	 * @return mixed|boolean
	 */
	public function getVendasRecentes()
	{
		//Obtenho informações da conta
		$sobreMim = $this->sobreMim();
		
		$res = $this->get('/orders/search/recent',['access_token' => $this->usuarioTokenSite->token,'seller' => $sobreMim['body']->id]);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		
		return false;
	}
	
	/**
	 * Retorna a etiqueta de envio de email de determinada venda.
	 * @param unknown $vendaId
	 */
	public function getEtiquetaCorreios($vendaId)
	{
		$res = $this->get('/shipment_labels',['access_token' => $this->usuarioTokenSite->token,'shipment_ids' => $vendaId]);
		
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		
		return false;
		
	}
	
	
	/**
	 * Retorna a lista de anuncios vinculados a conta cadastrada
	 */
	public function getAnuncios($query='',$offset=1)
	{
		//Obtenho informações da conta
		$sobreMim = $this->sobreMim();
		
		$res = $this->get('/users/'.$sobreMim['body']->id.'/items/search',['access_token' => $this->usuarioTokenSite->token,'offset' => $offset,'q' => $query]);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		
		return false;
	}
	
	
	/**
	 * Retorna a descrição do anuncio
	 * @param unknown $id
	 */
	public function getDescricaoAnuncio($id)
	{
		$res = $this->get('/items/'.$id.'/description',['access_token' => $this->usuarioTokenSite->token]);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
	}
	
	/**
	 * Retorna a lista de anuncios vinculados a conta cadastrada
	 */
	public function getAnunciosAtivos($query='',$offset=1)
	{
		//Obtenho informações da conta
		$sobreMim = $this->sobreMim();
		
		$res = $this->get('/users/'.$sobreMim['body']->id.'/items/search',['access_token' => $this->usuarioTokenSite->token,'offset' => $offset,'q' => $query,'status' => 'active']);
		if($res['httpCode'] == 200)
		{
			return $res['body'];
		}
		
		return false;
	}
	
	
	public function removePergunta($pergunta_id)
	{
	    $res = $this->delete('/questions/'.$pergunta_id,['access_token' => $this->usuarioTokenSite->token]);
	    if($res['httpCode'] == 200)
	    {
	        return $res['body'];
	    }
	  
	    return false;
	}
	
	public function respondePergunta($questionId,$resposta,$textoApresentacao='')
	{
		$questionId = str_replace('/questions/', '', $questionId);
		
		$item = [
				'text' => $resposta.$textoApresentacao,
				'question_id' => $questionId
		];
		
		$res = $this->post('/answers',$item,['access_token' => $this->usuarioTokenSite->token]);
		
		return $res;
	}
	
	
	public function getPergunta($questionId)
	{
		$questionId = str_replace('/questions/', '', $questionId);
		
		$res = $this->get('/questions/'.$questionId);
		
		return $res;
	}
	
	
	
	public function pausaAnuncioByCodigo($codigo)
	{
	    //PRIMEIRO FINALIZADA
	    $dadosAlterar = [
	        'status' => 'paused',
	    ];
	    
	    $res = $this->put('/items/'.$codigo,$dadosAlterar,array('access_token' => $this->usuarioTokenSite->token));
	    return $res;
	}
	
	
	public function alteraAnuncio($anuncio,$dadosAlterar)
	{
		$res = $this->put('/items/'.$anuncio->codigo_anuncio,$dadosAlterar,array('access_token' => $this->usuarioTokenSite->token));
		return $res;
	}
	
	
	public function sobreMim()
	{
		return $this->get('/users/me',['access_token' => $this->usuarioTokenSite->token]);
	}
	
	public function minhasVendas()
	{
	    
	    return false;
	    
		$sobreMim = $this->sobreMim();
		return $this->get('/orders/search/recent',['access_token' => $this->usuarioTokenSite->token,'seller' => $sobreMim['body']->id]);
	}
	
	public function removeAnuncioSimples($codigo)
	{
		//PRIMEIRO FINALIZADA
		$dadosAlterar = [
				'status' => 'closed',
		];
		
		$res = $this->put('/items/'.$codigo,$dadosAlterar,array('access_token' => $this->usuarioTokenSite->token));
		
		var_dump($res);
		
		$dadosAlterar = [
				'deleted' => 'true',
		];
		
		var_dump($res,$dadosAlterar);
		
		$res = $this->put('/items/'.$codigo,$dadosAlterar,array('access_token' => $this->usuarioTokenSite->token));
		
		return true;
	}
	
	public function removeAnuncio($anuncio)
	{
		//PRIMEIRO FINALIZADA
		$dadosAlterar = [
				'status' => 'closed',
		];
		
		$res = $this->alteraAnuncio($anuncio, $dadosAlterar);
		$dadosAlterar = [
				'deleted' => 'true',
		];
		
		//DEPOIS EXCLUI;
		if($res['httpCode'] == 200)
		{
			$res = $this->alteraAnuncio($anuncio, $dadosAlterar);
			if($res['httpCode'] == 200)
			{
				$anuncio->deleteWithRelated();
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Remove todos os anuncios do usuário direto no mercado livre.
	 * @param unknown $user_id
	 */
	public function actionRemoveTodosAnunciosForcado()
	{
		//$res = $meli->get('/myfeeds?app_id='.$usuarioTokenSite->site->app_id,['access_token' => $usuarioTokenSite->token]);
		//ENVIAZAP Obtenho informações sobre o usuário logado
		$sobreMim = $this->sobreMim();
		
		//Faço a paginação e excluo todos os anuncios.
		$offset = 1;
		while($offset)
		{
			$items = $this->get('/users/'.$sobreMim['body']->id.'/items/search',['access_token' => $this->usuarioTokenSite->token,'offset' => $offset]);
			
			foreach ($items['body']->results as $codigo_anuncio)
			{
			    echo $codigo_anuncio.PHP_EOL;
			    
				$this->removeAnuncioSimples($codigo_anuncio);
			}
			
			$offset = $offset + $items['body']->paging->limit;
			
			//Se o offset for menor continue
			if($offset > $items['body']->paging->total)
			{
				$offset = 0;
			}
		}
		
		exit;
	}
	
	/**
	 * Retorna o total de visitas dos itens do usuario.
	 * @param \DateTime $dataInicial
	 * @param \DateTime $dataFinal
	 * @endpoint https://api.mercadolibre.com/users/52366166/items_visits?date_from=2014-06-01T00:00:00.000-00:00&date_to=2014-06-10T00:00:00.000-00:00
	 * @method GET
	 * 
	 */
	public function getTotalVisitas(\DateTime $dataInicial, \DateTime $dataFinal)
	{
		$sobreMim = $this->sobreMim();
		
		$gmDataInicial = gmdate("Y-m-d\TH:i:s\Z", $dataInicial->getTimestamp());
		$gmDataFinal = gmdate("Y-m-d\TH:i:s\Z", $dataFinal->getTimestamp());
		
		$res = $this->get('/users/'.$sobreMim['body']->id.'/items_visits',['date_from' => $gmDataInicial,'date_to' => $gmDataFinal]);
		
		return $res;
	}
	
	public function getItems()
	{
		$sobreMim = $this->sobreMim();
		
		$items = $this->get('/users/'.$sobreMim['body']->id.'/items/search',['access_token' => $this->usuarioTokenSite->token]);
		
		return $items;
	}
	
	
	public function getCategorias()
	{
		return $this->get('/sites/MLB/categories');
	}
	
	
	public function getSubCategorias($categoriaId)
	{
		return $this->get('/categories/'.$categoriaId);
	}
	
	/**
	 * Refresh no token
	 * @return unknown
	 */
	public function getToken()
	{
		$date = new \DateTime();
		/*
		$date->modify('-2 hours');
		*/
		$res = $this->refreshAccessToken();
		
		if($res['httpCode'] == 200)
		{
			$expires =  $date->getTimestamp() + $res['body']->expires_in;
			$this->usuarioTokenSite->token = $res['body']->access_token;
			$this->usuarioTokenSite->refresh_token =  $res['body']->refresh_token;
			$this->usuarioTokenSite->expires_at = "$expires";
			if($this->usuarioTokenSite->save())
			{
				$this->access_token = $this->usuarioTokenSite->token;
				$this->refresh_token = $this->usuarioTokenSite->refresh_token;
			}
		}
		
	}
	
	
	public function cadastraItem($arrItem){
	    $res = $this->post('/items', $arrItem, array('access_token' => $this->access_token));
	    return $res;
	}
	
	
}


?>
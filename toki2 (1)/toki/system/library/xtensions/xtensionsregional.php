<?php
class XtensionsRegional {
	
	public function isValidCPF($cpf){
		$cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
		// Valida tamanho
		if (strlen($cpf) != 11){
			return false;
		}
		$invalidos = array('00000000000',
							'11111111111',
							'22222222222',
							'33333333333',
							'44444444444',
							'55555555555',
							'66666666666',
							'77777777777',
							'88888888888',
							'99999999999'
			
		);
		if (in_array($cpf, $invalidos)){
			return false;
		}
		// Calcula e confere primeiro dígito verificador
		for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--){
			$soma += $cpf[$i] * $j;
		}
		$resto = $soma % 11;
		if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)){
			return false;
		}
		// Calcula e confere segundo dígito verificador
		for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--){
			$soma += $cpf[$i] * $j;
		}
		$resto = $soma % 11;
		return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
	}
	
	public function isValidCNPJ($cnpj){
		$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
		// Valida tamanho
		if (strlen($cnpj) != 14){
			return false;
		}
		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++){
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)){
			return false;
		}
		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++){
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
	}
	
	//Validate EU VAT Number
	public function isValidEUVatNumber($vat_number){
		if(!file_exists(DIR_SYSTEM.'library/xtensions/eu_vat_validation.php')){
			return false;
		}
		require_once DIR_SYSTEM.'library/xtensions/eu_vat_validation.php';
		$vat = new EuVatValidation($vat_number);		
		return array('connection_error'=> $vat->getConnectionError(),'isValid' => $vat->isValidNumber,'country_code' => $vat->country_code);
	}
}

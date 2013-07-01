<?php

namespace app\models\fields\edit;

/**
 * Базавое поле мультиязычных полей
 */
class FieldMultiLang  extends FieldLang {


	protected $_langs;
	protected $_curLang;

	protected $_field = 'multilang';

	protected $_lang;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		$this->_langs = \Yii::app()->params['lang'];
		$this->_curLang = \Yii::app()->getLanguage();
		foreach($this->_langs as $lng)
			$this->_lang[$lng] = '';

		parent::__construct($data, $manager);
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		$f = strrpos($name, '_');
		if($f == strlen($name)-3) {
			$lang = substr($name, $f+1);
			if(in_array($lang, \Yii::app()->params['lang']) && substr($name, 0, $f) == $this->_name) return $this->_lang[$lang];
			elseif($name == $this->_name) return $this->getValue();
		}
		elseif($name == $this->_name) return $this->getValue();


		return parent::__get($name);
	}


	/**
	 * Установка свойств
	 * @param string $name
	 * @param mix $value
	 * @return mix 
	 */
	public function __set($name, $value) {
		if($name == $this->_name) return $this->setValue($value);


		return parent::__set($name, $value);
	}


	/**
	 * проверяем ввод хотя бы одного языка для обязательных полей
	 */
	public function multiLangCheck($attribute, $params) {
		if($this->_required) {
			foreach($this->_lang as $lang)
				if(!empty($lang)) return true;
		
			$this->addError($attribute, \Yii::t('nav', 'FIELD_MUST_ONE_LANGUAGE', array('{attribute}' => $this->getAttributeLabel($attribute))));
		}
	}


	/**
	 * имитируем обязательным основной язык для обязательных полей
	 */
	public function isAttributeRequired($attribute) {
		if($this->_required && $attribute == $this->_name . '_' . $this->_curLang) return true;


		return false;
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_isFiltered = false;
		$this->_multiLang = true;
		$this->setPurifierOptions(__CLASS__);


		parent::init();
	}


	public function initFromCache() {
		$this->_curLang = \Yii::app()->getLanguage();
	}


	// вызывается из CHtml::activeLabel
	public function getAttributeLabel($attribute) {
		$f = strrpos($attribute, '_');
		if($f == strlen($attribute)-3) {
			$lang = substr($attribute, $f+1);
			if(in_array($lang, \Yii::app()->params['lang']) && substr($attribute, 0, $f) == $this->_name) return \Yii::t($this->_labelDictionary, $this->_title);
			elseif($attribute == $this->_name) return \Yii::t($this->_labelDictionary, $this->_title);
		}
		elseif($attribute == $this->_name) return \Yii::t($this->_labelDictionary, $this->_title);

		return parent::getAttributeLabel($attribute);
	}


	public function getValue() {
		return $this->_lang[$this->_curLang];
	}


	public function setValue($value) {
		if($this->purifierOptions !== false) {
			$htmlPurifier = new \CHtmlPurifier();
			$htmlPurifier->options = $this->purifierOptions;
		}

		if(is_array($value)) {
			foreach($this->_langs as $lang)
				if(isset($value[$lang])) {
					if($this->purifierOptions !== false)
						$this->_lang[$lang] = $htmlPurifier->purify($value[$lang]);
					else
						$this->_lang[$lang] = $value[$lang];
				}
		}
		else {
			if($this->purifierOptions !== false)
				$this->_lang[$this->_curLang] = $htmlPurifier->purify($value);
			else
				$this->_lang[$this->_curLang] = $value;
		}
	}


	public function getValueText() {
		return $this->_lang[$this->_curLang];
	}


	public function getMultiLang() {
		return $this->_multiLang;
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		return \app\managers\Manager::fromArray((array)$this->_lang);
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		$value = (array)$value;

		foreach($this->_langs as $lang) {
			$val = current($value);
			if($val) $this->_lang[$lang] = $val;
			next($value);
		}


		return true;
	}


	public function unPackData($data) {
		$data = $this->unPackFromArray($data);
		// для нумерации массива с 1
		$return[] = '';
		foreach($data as $id => $value)
			$return[] = $this->_manager->toArray($value, false);

		// для нумерации массива с 1
		unset($return[0]);


		return $return;
	}


	protected function unPackFromArray($data) {
		$data = substr($data, 2, -2);


		return explode('},{', $data);
	}


	/**
	 * Событие выполняется до сохранения объявления (insert)
	 * @return boolean 
	 */
	public function beforeInsert() {
		/* выбираем язык источник согласно ТЗ */
		/* английский */
		if(!empty($this->_lang['en'])) $source = 'en';
		/* пользователя */
		elseif(!empty($this->_lang[$this->_curLang])) $source = $this->_curLang;
		/* первый введеный */
		elseif(!empty($this->_lang)) {
			foreach($this->_lang as $lang => &$value)
				if(!empty($value)) {
					$source = $lang;
					break;
				}
		}

		/* переводим */
		if(!empty($this->_lang)) {
			foreach($this->_lang as $lang => &$value) {
				if($lang == $source) continue;
				if(empty($value)) $value = $this->_translate($this->_lang[$source], $source, $lang);
			}
		}


		return true;
	}


	/**
	 * Событие выполняется до сохранения объявления (update)
	 * @return boolean 
	 */
	public function beforeUpdate() {
		/* выбираем язык источник согласно ТЗ */
		/* английский */
		if(!empty($this->_lang['en'])) $source = 'en';
		/* пользователя */
		elseif(!empty($this->_lang[$this->_curLang])) $source = $this->_curLang;
		/* первый введеный */
		elseif(!empty($this->_lang)) {
			foreach($this->_lang as $lang => &$value)
				if(!empty($value)) {
					$source = $lang;
					break;
				}
		}

		/* переводим */
		if(!empty($this->_lang)) {
			foreach($this->_lang as $lang => &$value) {
				if($lang == $source) continue;
				if(empty($value)) $value = $this->_translate($this->_lang[$source], $source, $lang);
			}
		}


		return true;
	}


	protected function _translate($text, $source, $target) {
		// GET https://www.googleapis.com/language/translate/v2?key={INSERT-YOUR-KEY}&source=en&target=de&q=Hello%20world
		$params = http_build_query(
			array(
				'key' => \Yii::app()->params['googleTranslateApiKey'],
				'source' => $source,
				'target' => $target,
				'q' => $text,
			)
		);

		$opts = array('https' =>
			array(
				'method'  => 'POST',
				'timeout' => 20,
				'ignore_errors' => '1',
			)
		);

		$contentLength = strlen($params);
		$context  = stream_context_create($opts);
		$headers = <<<DATA
POST /language/translate/v2 HTTP/1.0
Host: www.googleapis.com
X-HTTP-Method-Override: GET
Referer: http://geojoy.com/needTranslate
Content-Type: application/x-www-form-urlencoded
Content-Length: $contentLength
Connection: Close

$params
DATA;

		$fp = fsockopen ('ssl://www.googleapis.com', 443, $errno, $errstr, 10);
		if(!$fp) {
			\Yii::app()->appLog->object('OBJECT_TRANSLATE_TITLE', 'OBJECT_TRANSLATE_ERROR_SEE_LOG', array(
				'{object}' => \CHtml::link($this->getManager()->getId(), array('/site/objects/view', 'id' => $this->getManager()->getId())),
				'{error}' => $errstr,
				'{code}' => $errno,
			), \app\components\AppLog::TYPE_ERROR);
			\Yii::log("Error open soket.", \CLogger::LEVEL_ERROR);
		}
		else {
			stream_set_timeout($fp, 5);
			fputs($fp, $headers);
			$body = '';
			while(!feof($fp)) {
				$body .= fgets($fp, 1024);}

			fclose ($fp);
		}

		$f = strpos($body, "\r\n\r\n");
		$headers = substr($body, 0, $f);
		$body = substr($body, $f+4);
		$translate = json_decode($body);
		if($translate == null) {
			$header = explode("\r\n", $headers);
			list($header, $code, $message) = explode(' ', $header[0], 3);
			\Yii::app()->appLog->object('OBJECT_TRANSLATE_TITLE', 'OBJECT_TRANSLATE_ERROR_SEE_LOG', array(
				'{object}' => \CHtml::link($this->getManager()->getId(), array('/site/objects/view', 'id' => $this->getManager()->getId())),
				'{error}' => $message,
				'{code}' => $code,
			), \app\components\AppLog::TYPE_ERROR);
			\Yii::log("Error in translation.\nheaders:\n$headers\nbody:\n$body\n", \CLogger::LEVEL_ERROR);

			return null;
		}

		/* формат ошибки
		{
			"error": {
				"errors": [
					{
						"domain": "usageLimits",
						"reason": "accessNotConfigured",
						"message": "Access Not Configured"
					}
				],
				"code": 403,
				"message": "Access Not Configured"
			}
		}

		перевод
		{
			"data": {
				"translations": [
					{
						"translatedText": "Title rental"
					}
				]
			}
		}
		*/

		if(!empty($translate->error)) {
			\Yii::app()->appLog->object('OBJECT_TRANSLATE_TITLE', 'OBJECT_TRANSLATE_ERROR', array(
				'{object}' => \CHtml::link($this->getManager()->getId(), array('/site/objects/view', 'id' => $this->getManager()->getId())),
				'{error}' => $translate->error->message,
				'{code}' => $translate->error->code,
			), \app\components\AppLog::TYPE_ERROR);

			return null;
		}
		else return $translate->data->translations[0]->translatedText;
	}


}

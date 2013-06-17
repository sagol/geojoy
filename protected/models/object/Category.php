<?php

namespace app\models\object;

/**
 * Категории объявлений
 */
class Category extends \CActiveRecord {


	/**
	 * Типы объявлений
	 * @var array 
	 */
	private static $_objectType = array();
	/**
	 * Дерево категорий
	 * @var array 
	 */
	private static $_tree = array();
	/**
	 * Категории
	 * @var array 
	 */
	private static $_category = array();
	/**
	 * Сортировка
	 * @var array 
	 */
	private static $_order = array();
	/**
	 * Значиние до изменения
	 * @var string 
	 */
	private $_oldTree = null;
	private $_oldDisabled = null;
	/**
	 * 
	 * @var string 
	 */
	private $_image = null;

	public $delete = false;


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\Category 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_category';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('tree, name, alias', 'required'),
			array('alias', 'aliasCheck'),
			array('idobj_type, disabled, moderate', 'numerical', 'integerOnly' => true),
			array('tree', 'length', 'max' => 12),
			array('img', 'length', 'max' => 100),
			array('tree', 'treeCheck'),
			array('alias, description, delete', 'safe'),
			array('img', 'file', 'maxFiles' => 1, 'allowEmpty' => true, 'types' => 'jpg, jpeg, gif, png'),
			array('idobj_category, idobj_type, tree, name, alias, description, moderate, disabled, img', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Проверка алиаса
	 * @param string $attribute
	 * @param array $params 
	 */
	public function aliasCheck($attribute, $params) {
		$value = $this->alias;
		if(is_numeric($value)) {
			$attributeLabels = $this->attributeLabels();
			$this->addError($attribute, \Yii::t('nav', 'FIELD_NOT_SET_NUMERIC', array('{field}' => $attributeLabels[$attribute])));
			unset($attributeLabels);
		}

		if(strlen($this->tree) == 2 && in_array($value, \Yii::app()->params['forbiddenCategoryWords'])) {
			$attributeLabels = $this->attributeLabels();
			$this->addError($attribute, \Yii::t('nav', 'FIELD_NOT_SET_VALUE', array('{field}' => $attributeLabels[$attribute], 'value' => $value)));
			unset($attributeLabels);
		}


		if(substr($this->tree, -1, 1) == 'L') $tree = substr($this->tree, 0, -3) . '__';
		else $tree = substr($this->tree, 0, -2) . '__';

		$sql = 'SELECT idobj_category 
			FROM obj_category 
			WHERE alias = :alias AND tree LIKE :tree';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':alias', $value, \PDO::PARAM_STR);
		$command->bindParam(':tree', $tree, \PDO::PARAM_STR);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false && $data['idobj_category'] != $this->idobj_category) {
			$attributeLabels = $this->attributeLabels();
			$this->addError($attribute, \Yii::t('nav', 'FIELD_VALUE_NOT_UNIC', array('{field}' => $attributeLabels[$attribute], 'value' => $value)));

		}
	}



	/**
	 * Проверка дерева
	 * @param string $attribute
	 * @param array $params 
	 */
	public function treeCheck($attribute, $params) {
		if(strlen($this->tree) > strlen($this->_oldTree) && strpos($this->tree, $this->_oldTree) === 0)
			$this->addError($attribute, \Yii::t('nav', 'NOT_MOVE_TREE'));
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_category' => 'ID',
			'idobj_type' => \Yii::t('admin', 'CATEGORY_FIELD_TYPE'),
			'tree' => \Yii::t('admin', 'CATEGORY_FIELD_TREE'),
			'name' => \Yii::t('admin', 'CATEGORY_FIELD_NAME'),
			'disabled' => \Yii::t('admin', 'CATEGORY_FIELD_DISABLED'),
			'alias' => \Yii::t('admin', 'CATEGORY_FIELD_ALIAS'),
			'order' => \Yii::t('admin', 'CATEGORY_FIELD_ORDER'),
			'moderate' => \Yii::t('admin', 'CATEGORY_FIELD_MODERATE'),
			'description' => \Yii::t('admin', 'CATEGORY_FIELD_DESCRIPTION'),
			'img' => \Yii::t('admin', 'CATEGORY_FIELD_IMG'),
			'download' => \Yii::t('admin', 'CATEGORY_FIELD_DOWNLOAD'),
			'delete' => \Yii::t('admin', 'CATEGORY_FIELD_DELETE'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		if(!is_numeric($this->idobj_type)) $this->idobj_type = array_search($this->idobj_type, $this->idobj_type_arr);

		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_category)) $this->idobj_category = '';
		if(!is_numeric($this->idobj_type)) $this->idobj_type = '';
		if(!is_numeric($this->moderate)) $this->moderate = '';
		if(!is_numeric($this->disabled)) $this->disabled = '';

		$criteria = new \CDbCriteria;
		$criteria->compare('idobj_category', $this->idobj_category);
		$criteria->compare('idobj_type', $this->idobj_type);
		$criteria->compare('tree', $this->tree, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('disabled', $this->disabled);
		$criteria->compare('moderate', $this->moderate);
		$criteria->compare('alias', $this->alias, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array('defaultOrder' => 'tree ASC'),
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Операции перед сохранением
	 * @return boolean 
	 */
	public function beforeSave() {
		$this->_image = \CUploadedFile::getInstance($this, 'img');
		if(is_object($this->_image) && !$this->_image->getHasError()) {
			$this->img = $this->_image->getName();

			$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']);
			if(!file_exists($uploadDir . DS . 'tiles')) 
				if(!mkdir($uploadDir . DS . 'tiles')) {
					\Yii::app()->appLog->add('upload', 'CATEGOGY_UPLOAD_IMAGE_TITLE', 'CATEGOGY_UPLOAD_IMAGE_ERROR_CREATE_DIR', array('{dir}' => $uploadDir . DS . 'tiles'), \app\components\AppLog::TYPE_ERROR);
					\Yii::log('Error create dir (' . $uploadDir . DS . 'tiles) for upload image category.', \CLogger::LEVEL_ERROR);
				}
			if(!$this->_image->saveAs($uploadDir . DS . 'tiles' . DS . $this->img)) {
				$this->addError('img', \Yii::t('nav', 'ERROR_FILE_NOT_SAVE'));
				return false;
			}
			else $this->img = '/tiles/' . $this->img;
		}

		if($this->isNewRecord) {
			// добавляется последним
			if(substr($this->tree, -1, 1) == 'L') {
				$this->tree = substr($this->tree, 0, -1);
				return true;
			}
			else { // для добавления нужно освободить место в tree, сместить вниз
				$like = substr($this->tree, 0, -2);
				$left = strlen($this->tree)-2;
				$mid = $left+1;
				$right = $mid+2;
				$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)+1)::varchar, 2, '0') || substring(tree from $right) 
					WHERE tree LIKE '$like%' AND tree >= '$this->tree'";
				\Yii::app()->db->createCommand($sql)->execute();
			}
		}
		elseif($this->delete) $this->img = null;

		return true;
	}


	/**
	 * Операции после сохранения
	 * @return boolean 
	 */
	public function afterSave() {
		$cache = \Yii::app()->cache;
		// удаление из кеша структуры категорий
		$cache->delete('category');
		// удаление из кеша виджета фильтра
		// не используется на данный момент
		// $cache->delete('widgetFilter');
		// удаление из кеша фильтра
		$cache->delete('filter');

		$db = \Yii::app()->db;

		if($this->disabled != $this->_oldDisabled) {
			if($this->disabled) {
				$disabled = 1;
				$disabledWhere = 0;
			}
			else  {
				$disabled = 0;
				$disabledWhere = 1;
			}

			$sql = "UPDATE objects SET disabled = :disabled 
				WHERE idobj_category = :category AND disabled = :disabledWhere";
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':disabled', $disabled, \PDO::PARAM_INT);
			$command->bindParam(':disabledWhere', $disabledWhere, \PDO::PARAM_INT);
			$command->bindValue(':category', $this->idobj_category, \PDO::PARAM_INT);
			$rowCount = $command->execute();

		}

		// изменений нету
		if($this->tree == $this->_oldTree) return true;
		if($this->isNewRecord) return true;

		if(substr($this->tree, -1, 1) == 'L') {
			$this->tree = substr($this->tree, 0, -1);
			$last = true;
		}
		else $last = false;

		// перемещение
		$like = substr($this->tree, 0, -2);
		$likeOld = substr($this->_oldTree, 0, -2);


		if($like == $likeOld) { // перемещение внутри своей категории
			$right = strlen($this->_oldTree)+1;
			// перемещение подкатегорий на новое место
			$sql = "UPDATE obj_category SET tree = '**$this->tree' || substring(tree from $right) 
				WHERE tree like '$this->_oldTree%' AND NOT tree = '$this->_oldTree'";
			$db->createCommand($sql)->execute();

			$left = strlen($this->tree)-2;
			$mid = $left+1;
			$right = $mid+2;

			if($this->_oldTree > $this->tree) // сдвиг вниз
				$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)+1)::varchar, 2, '0') || substring(tree from $right) 
					WHERE tree NOT LIKE '**%' AND tree like '$like%' AND tree >= '$this->tree' AND tree < '$this->_oldTree' AND NOT idobj_category = $this->idobj_category";
			else {// сдвиг вверх
				$from = $this->_treeUp($this->_oldTree);
				$to = $this->_treeUp($this->tree);
				$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)-1)::varchar, 2, '0') || substring(tree from $right) 
					WHERE tree NOT LIKE '**%' AND tree like '$like%' AND tree >= '$from' AND tree < '$to' AND NOT idobj_category = $this->idobj_category";
			}
			$db->createCommand($sql)->execute();

			// перемещение подкатегорий на новое место
			$sql = "UPDATE obj_category SET tree = substring(tree from 3) 
				WHERE tree like '**%'";
			$db->createCommand($sql)->execute();

			// задание дерева при выборе маркера последняя категория
			if($last) {
				// номер позиции последнего элемента на 1 больше кол-ва элементов (в текущем уровне)
				// и при перемещении внутри текущего уровня на эту единицу учитывать
				if(strlen($this->tree) == strlen($this->_oldTree) && !$this->isNewRecord) $this->tree = $this->_treeDown($this->tree);
				$sql = "UPDATE obj_category SET tree = '$this->tree' 
					WHERE idobj_category = $this->idobj_category";
				$db->createCommand($sql)->execute();
			}
		}
		else {
			$left = strlen($this->tree)-2;
			$mid = $left+1;
			$right = $mid+2;

			if(!$last) {
				// освобождение места для перемещения на новое место
				$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)+1)::varchar, 2, '0') || substring(tree from $right) 
					WHERE tree LIKE '$like%' AND tree >= '$this->tree' AND NOT idobj_category = $this->idobj_category";
				$db->createCommand($sql)->execute();
			}

			$left = strlen($this->_oldTree)-2;
			$mid = $left+1;
			$right = $mid+2;
			// перемещение подкатегорий выбранной категории на новое место
			$sql = "UPDATE obj_category SET tree = '$this->tree' || substring(tree from $right) WHERE tree like '$this->_oldTree%'";
			$db->createCommand($sql)->execute();

			if($last) {
				// номер позиции последнего элемента на 1 больше кол-ва элементов (в текущем уровне)
				// и при перемещении внутри текущего уровня на эту единицу учитывать
				if(strlen($this->tree) == strlen($this->_oldTree) && !$this->isNewRecord) $this->tree = $this->_treeDown($this->tree);
				// задание кода дерева при выборе маркера последняя категория
				$sql = "UPDATE obj_category SET tree = '$this->tree' 
					WHERE idobj_category = $this->idobj_category";
				$db->createCommand($sql)->execute();
			}

			// поднятие вверх на освободившиеся место
			$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)-1)::varchar, 2, '0') || substring(tree from $right) 
				WHERE tree LIKE '{$likeOld}__%' AND tree >= '$this->_oldTree'";
			$db->createCommand($sql)->execute();
		}
	}


	/**
	 * Значение на позицию больше
	 * @param string $tree
	 * @return string 
	 */
	private function _treeUp($tree) {
		$len = strlen($tree);
		if(!$len) return '';

		$level = substr($tree, $len-2);
		$level++;


		return substr($tree, 0, $len-2) . str_pad($level, 2, '0', STR_PAD_LEFT);
	}


	/**
	 * Значение на позицию меньше
	 * @param string $tree
	 * @return string 
	 */
	private function _treeDown($tree) {
		$len = strlen($tree);
		if(!$len) return '';

		$level = substr($tree, $len-2);
		$level--;


		return substr($tree, 0, $len-2) . str_pad($level, 2, '0', STR_PAD_LEFT);
	}


	/**
	 * Операции перед удалением
	 * @return boolean 
	 */
	public function beforeDelete() {
		// выборка id категорий, для удаления объявлений
		$sql = "SELECT idobj_category 
			FROM obj_category 
			WHERE tree LIKE '$this->tree%'";
		$dataReader = \Yii::app()->db->createCommand($sql)->query();

		while(($data = $dataReader->read()) !== false)
			$category[] = $data['idobj_category'];

		// удаление объявлений
		$modelObject = new \app\models\object\Object;
		$modelObject->deleteMany(array('category' => $category));

		// удаление подкатегорий
		if(count($category) > 1) { // если равно 1, подкатегорий нет
			$sql = "DELETE FROM obj_category 
				WHERE tree LIKE '$this->tree%' AND NOT tree = '$this->tree' ";
			if(!\Yii::app()->db->createCommand($sql)->execute()) return false;
		}

		$len = strlen($this->tree)-2;
		$lenFrom = $len+1;
		$like = substr($this->tree, 0, -2);
			$left = strlen($this->tree)-2;
			$mid = $left+1;
			$right = $mid+2;

		// перемещение категорий
		$sql = "UPDATE obj_category SET tree = substring(tree for $left) || lpad((cast(substring(tree from $mid for 2) as int4)-1)::varchar, 2, '0') || substring(tree from $right) 
			WHERE tree LIKE '$like%' AND tree > '$this->tree'";
		\Yii::app()->db->createCommand($sql)->execute();

		return true;
	}


	/**
	 * Операции после удаления
	 */
	public function afterDelete() {
		$cache = \Yii::app()->cache;
		// удаление из кеша структуры категорий
		$cache->delete('category');
		// удаление из кеша виджета фильтра
		// не используется на данный момент
		// $cache->delete('widgetFilter');
		// удаление из кеша фильтра
		$cache->delete('filter');
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'tree_arr') return $this->tree();
		elseif($name == 'tree_arr1') return $this->tree(true, null, false);

		elseif($name == 'idobj_type_val') return $this->idObjectType(false, parent::__get('idobj_type'), false);
		elseif($name == 'idobj_type_arr') return $this->idObjectType();
		elseif($name == 'idobj_type_arr1') return $this->idObjectType(true, null, false);

		elseif($name == 'order_arr') return $this->order();

		else return parent::__get($name);
	}


	/**
	 * Задание значений
	 * @param string $name
	 * @param mixed $value
	 * @return mixed 
	 */
	public function __set($name, $value) {
		// сохранение значение дерева до сохранения изменений
		if($name == 'tree' && $this->_oldTree === null) $this->_oldTree = $this->tree;
		if($name == 'disabled' && $this->_oldDisabled === null) $this->_oldDisabled = $this->disabled;

		return parent::__set($name, $value);
	}


	/**
	 * Типы объявлений
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function idObjectType($arr = true, $id = null, $all = true) {
		if(empty(self::$_objectType)) {
			self::$_objectType[''] = \Yii::t('nav', 'ALL');
			self::$_objectType[0] = \Yii::t('nav', 'NOT_SET');

			$sql = "SELECT * 
				FROM obj_type";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_objectType[$data['idobj_type']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_objectType;

			return @self::$_objectType[$id];
		}
		else {
			$_objectType = self::$_objectType;
			unset($_objectType['']);

			if($arr) return $_objectType;

			return @$_objectType[$id];
		}
	}


	/**
	 * Категории
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function tree($arr = true, $id = null, $all = true) {
		if(empty(self::$_tree)) {
			self::$_tree[''] = \Yii::t('nav', 'ALL');

			$sql = "SELECT tree, name 
				FROM obj_category 
				ORDER BY tree";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_tree[$data['tree']] = str_pad('', (strlen($data['tree'])-2)*2, '-') . $data['name'];
		}

		if($all) {
			if($arr) return self::$_tree;

			return @self::$_tree[$id];
		}
		else {
			$_tree = self::$_tree;
			unset($_tree['']);

			if($arr) return $_tree;

			return @$_tree[$id];
		}
	}


	/**
	 * Порядок категорий (для создания/перемещения категории)
	 * @return array 
	 */
	public function order() {
		if(empty(self::$_order)) {
			$order = $this->tree();
			unset($order['']);

			$lastId = '00';
			foreach($order as $id => $name) {
				if(strlen($id) < strlen($lastId)) {
					$tmpIdLast = $lastId;
					while(strlen($id) < strlen($tmpIdLast)) {
						$tmpId = substr($tmpIdLast, -2, 2);
						$tmpId++;
						$tmpId = substr($tmpIdLast, 0, -2) . str_pad('', strlen($tmpId), '0') . $tmpId;
						self::$_order[$tmpId . 'L'] = str_pad('', (strlen($tmpId)-2)*2, '-') . \Yii::t('nav', 'LAST');
						$tmpIdLast = substr($tmpIdLast, 0, -2);
					}
				}

				self::$_order[$id] = $name;
				$lastId = $id;

				// добавление следующего уровня, максимально 6 (половина длины поля tree таблицы obj_category)
				if(!isset($order[$id . '01']) && strlen($id . '01') < 12) self::$_order[$id . '01L'] = str_pad('', (strlen($id))*2, '-') . \Yii::t('nav', 'CHILD');
			}

			// создание последнего для текущего уровня
			$tmpId = substr($lastId, -2, 2);
			$tmpId++;
			$tmpId = substr($lastId, 0, -2) . str_pad('', strlen($tmpId), '0') . $tmpId;
			self::$_order[$tmpId . 'L'] = str_pad('', (strlen($tmpId)-2)*2, '-') . \Yii::t('nav', 'LAST');

			// создание последнего для первого уровня
			if(strlen($lastId) > 2) {
				$tmpId = substr($lastId, 0, 2);
				$tmpId++;
				$tmpId = str_pad('', strlen($tmpId), '0') . $tmpId;
				self::$_order[$tmpId . 'L'] = \Yii::t('nav', 'LAST');
			}
			unset($order);
		}


		return self::$_order;
	}


}
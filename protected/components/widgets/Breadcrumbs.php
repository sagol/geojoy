<?php

namespace app\components\widgets;

/**
 * Виджет хлебных крошек, основан на CBreadcrumbs html выделен в вид
 */
class Breadcrumbs extends \app\components\core\Widget {


	/**
	 * @var string the tag name for the breadcrumbs container tag. Defaults to 'div'.
	 */
	public $tagName = 'ul';
	/**
	 * @var array the HTML attributes for the breadcrumbs container tag.
	 */
	public $htmlOptions = array('class' => 'breadcrumb');
	/**
	 * @var boolean whether to HTML encode the link labels. Defaults to true.
	 */
	public $encodeLabel = true;
	/**
	 * @var string the first hyperlink in the breadcrumbs (called home link).
	 * If this property is not set, it defaults to a link pointing to {@link CWebApplication::homeUrl} with label 'Home'.
	 * If this property is false, the home link will not be rendered.
	 */
	public $homeLink;
	/**
	 * @var array list of hyperlinks to appear in the breadcrumbs. If this property is empty,
	 * the widget will not render anything. Each key-value pair in the array
	 * will be used to generate a hyperlink by calling CHtml::link(key, value). For this reason, the key
	 * refers to the label of the link while the value can be a string or an array (used to
	 * create a URL). For more details, please refer to {@link CHtml::link}.
	 * If an element's key is an integer, it means the element will be rendered as a label only (meaning the current page).
	 *
	 * The following example will generate breadcrumbs as "Home > Sample post > Edit", where "Home" points to the homepage,
	 * "Sample post" points to the "index.php?r=post/view&id=12" page, and "Edit" is a label. Note that the "Home" link
	 * is specified via {@link homeLink} separately.
	 *
	 * <pre>
	 * array(
	 *     'Sample post' => array('post/view', 'id' => 12),
	 *     'Edit',
	 * )
	 * </pre>
	 */
	public $links = array();
	/**
	 * @var string the separator between links in the breadcrumbs. Defaults to ' &raquo; '.
	 */
	public $separator = '<span class="divider">/</span>';


	/**
	 * Renders the content of the portlet.
	 */
	public function run() {
		$links = array();

		// добавляем главную
		if($this->homeLink === null) $links['NAV_HOME'] = \Yii::app()->createAbsoluteUrl('/');
		elseif($this->homeLink !== false) $links[$this->homeLink] = $this->homeLink;

		// объединяем массивы
		if(!empty($this->links)) {
			foreach($this->links as $name => $value) {
				if($name === null || $name === '') $name = '-';
				$links[$name] = $value;
			}
		}

		// выводим вид
		if(!empty($links)) {
			// получаем последний элемент крошек
			$last = array_keys($links);
			$last = end($last);
			if(is_array($links[$last])) {
				$links[] = $last;
				unset($links[$last]);
			}

			$config['tagName'] = $this->tagName;
			$config['htmlOptions'] = $this->htmlOptions;
			$config['encodeLabel'] = $this->encodeLabel;
			$config['homeLink'] = $this->homeLink;
			$config['separator'] = $this->separator;
			$config['links'] = $links;

			$this->render('breadcrumbs', $config);
		}
	}


}
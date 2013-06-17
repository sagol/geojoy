<?php

return array(

	'EXIT_USER_TITLE' => 'Выход пользователя',
	'EXIT_USER' => 'Пользователь {user} выполняет выход',
	'EXIT_USER_OK' => 'Пользователь {user} успешно вышел',

	'EMAIL_VALIDATE_TITLE' => 'Подтверждения email`а',
	'EMAIL_VALIDATE' => 'Отправлено письмо для подтверждения email`а пользователя {user} на адрес {email}',
	'EMAIL_VALIDATE_ERROR' => 'Ошибка {error} отправки письма для подтверждения email`а пользователя {user} на адрес {email}',
	'EMAIL_VALIDATE_ERROR_TABLE' => 'Ошибка при создании записи в таблице для пользователя {user}',

	'EMAIL_RESET_TITLE' => 'Письмо для восстановления пароля',
	'EMAIL_RESET' => 'Отправлено письмо для восстановления пароля пользователю {user} на адрес {email}',
	'EMAIL_RESET_ERROR' => 'Ошибка {error} отправки письма для восстановления пароля пользователю {user} на адрес {email}',
	'EMAIL_RESET_ERROR_TABLE' => 'Ошибка при создании записи в таблице для пользователя {user}',

	'FILTER_TITLE' => 'Фильтр',
	'FILTER' => 'Ошибка настроек фильтра, используется отключенное поле ({field})',

	'OBJECT_CREATE_TITLE' => 'Создание объявления',
	'OBJECT_CREATE_ERROR' => 'Ошибка создания объявления у пользователя {user}',
	'OBJECT_CREATE' => 'Объявление {object} успешно создано',

	'OBJECT_UPDATE_TITLE' => 'Редактирование объявления',
	'OBJECT_UPDATE_ERROR' => 'Ошибка редактирования объявления у пользователя {user}',
	'OBJECT_UPDATE' => 'Объявление {object} успешно создано',

	'OBJECT_DELETE_TITLE' => 'Удаление объявления',
	'OBJECT_DELETE' => 'Объявление {object} успешно удалено',
	'OBJECT_DELETE_ERROR' => 'Ошибка удаления объявления {object} у пользователя {user}',

	'OBJECT_UP_TITLE' => 'Поднятие объявления вверх',
	'OBJECT_UP' => 'Успешное поднятие вверх объявления {object}',
	'OBJECT_UP_ERROR' => 'Ошибка при поднятии вверх объявления {object}',

	'OBJECT_MODERATE_TITLE' => 'Модерация объявления',
	'OBJECT_MODERATE' => 'Успешное изменении статуса модерации на выполнена для объявление {object}',
	'OBJECT_MODERATE_ERROR' => 'Ошибка при изменении статуса модерации на выполнена для объявление {object}',

	'OBJECT_SPAM_TITLE' => 'Спам',
	'OBJECT_SPAM_EXACTLY' => 'Объявление {object} перемещено в спам',
	'OBJECT_SPAM_POSSIBLY' => 'Объявление {object} помечено как спам',
	'OBJECT_SPAM_RESET' => 'У объявление {object} спам статус сброшен',
	'OBJECT_SPAM_NOT' => 'Объявлению {object} присвоен статус не спам',
	'OBJECT_SPAM_EXACTLY_ERROR' => 'Ошибка при операции: объявление {object} перемещено в спам',
	'OBJECT_SPAM_POSSIBLY_ERROR' => 'Ошибка при операции: объявление {object} помечено как спам',
	'OBJECT_SPAM_RESET_ERROR' => 'Ошибка при операции: у объявление {object} спам статус сброшен',
	'OBJECT_SPAM_NOT_ERROR' => 'Ошибка при операции: объявлению {object} присвоен статус не спам',

	'OBJECT_TRANSLATE_TITLE' => 'Перевод объявления',
	'OBJECT_TRANSLATE_ERROR' => 'Ошибка "#{code} {error}" перевода объявления {object}',
	'OBJECT_TRANSLATE_ERROR_SEE_LOG' => 'Ошибка "#{code} {error}" перевода объявления {object}, детали в логе.',

	'KARMA_TITLE' => 'Карма',
	'KARMA_APPROVED' => 'Карма {karma} у пользователя {id} успешно утверждена',
	'KARMA_APPROVED_ERROR' => 'Ошибка утверждения кармы {karma} у пользователя {id} успешно утверждена',
	'KARMA_REJECTED' => 'Карма {karma} у пользователя {id} успешно отклонена',
	'KARMA_REJECTED_ERROR' => 'Ошибка отклонения кармы {karma} у пользователя {id} успешно отклонена',

	'USER_ENTER_TITLE' => 'Вход пользователя',
	'USER_ENTER' => 'Пользователь {user} успешно вошел',
	'USER_ENTER_ERROR' => 'Ошибка входа пользователя {email}, {error}',

	'PASSWORD_RECOVERY_TITLE' => 'Восстановление пароля',
	'PASSWORD_RECOVERY' => 'Ошибка восстановления пароля для email`a {email}',

	'USER_DELETE_TITLE' => 'Удаление пользователя',
	'USER_DELETE' => 'Пользователь {user} успешно удален',
	'USER_DELETE_ERROR' => 'Ошибка удаления пользователя {user}',

	'USER_REGISTRATION_TITLE' => 'Регистрация пользователя',
	'USER_REGISTRATION' => 'Пользователь {user} успешно зарегистрирован',
	'USER_REGISTRATION_ERROR' => 'Ошибка регистрации пользователя с адресом {email}',

	'USER_ACTIVATION_TITLE' => 'Активация пользователя',
	'USER_ACTIVATION' => 'Пользователь {user} успешно активировался',
	'USER_ACTIVATION_ERROR' => 'Ошибка активации пользователя {user}',

	'MAIL_ACTIVATION_TITLE' => 'Отправка письма для активации',
	'MAIL_ACTIVATION' => 'Отправлено письмо для активации пользователю {user} на адрес {email}',
	'MAIL_ACTIVATION_ERROR' => 'Ошибка {error} отправки письма для активации пользователю {user} на адрес {email}',

	'USER_CREATE_TITLE' => 'Создание пользователя',
	'USER_CREATE' => 'Пользователь {user} успешно создан',

	'USER_STATUS_EDIT_TITLE' => 'Изменение статуса пользователя',
	'USER_STATUS_EDIT' => 'Статус пользователя {user} вручную изменен с {oldStatus} на {status}',

	'MAIL_PRIVATE_MESSAGE_TITLE' => 'Рассылка',
	'MAIL_PRIVATE_MESSAGE' => 'Отправлено уведомление о получении нового личного сообщения {message}  на адрес {email}',
	'MAIL_PRIVATE_MESSAGE_ERROR' => 'Ошибка {error} отправки уведомления о получении нового личного сообщения {message}  на адрес {email}',

	'MAIL_NEWS_TITLE' => 'Новости сайта',
	'MAIL_NEWS' => 'Отправлено уведомление о новости {news} пользователю {user} на адрес {email}',
	'MAIL_NEWS_ERROR' => 'Ошибка {error} отправки уведомления о новости {news} пользователю {user} на адрес {email}',

	'CATEGOGY_UPLOAD_IMAGE_TITLE' => 'Категории объявлений',
	'CATEGOGY_UPLOAD_IMAGE_ERROR_CREATE_DIR' => 'Ошибка создания папки ({dir}) для загрузки изображения категории',

);
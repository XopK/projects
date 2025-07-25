<?php

return [
    // Запись пользователя в набор //
    'register_user_on_group' => [

        'params' => [
            'owner',
            'client',
            'title_group',
            'date_time',
            'count',
            'url'
        ],

        'telegram' => [
            'owner' => [
                'message' => "🎉 *Новый участник в вашем наборе!*\n\n"
                    . "👤 Пользователь :client записался в набор:\n\n"
                    . "🏷 *Название набора:* :title_group\n"
                    . "📅 *Дата и время:*  :date_time\n"
                    . "👥 *Всего участников:* :count\n\n"
                    . "Пожалуйста, подтвердите или отклоните заявку.\n\n"
                    . "[Посмотреть детали набора](:url)\n\n"
                    . "📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ],
            'client' => [
                'message' => "🎉 Вы успешно записались в набор!\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "📅 *Дата и время:* :date_time \n"
                    . "👥 *Всего участников:* :count\n\n"
                    . "[Посмотреть детали набора.](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ],
        ],

        'email' => [
            'owner' => [
                'subject' => 'Новый участник в вашем наборе',
                'message' => "Здравствуйте, <strong>:owner</strong>!<br><br>"
                    . "<em>:client</em> записался в набор '<strong>:title_group</strong>', который пройдёт <u>:date_time</u>.<br><br>"
                    . "Всего участников: <strong>:count</strong><br><br>"
                    . "Пожалуйста, подтвердите или отклоните заявку.<br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ],

            'client' => [
                'subject' => 'Вы записались в набор!',
                'message' => "Здравствуйте, <strong>:client</strong>!<br><br>"
                    . "Вы успешно записались в набор '<strong>:title_group</strong>', который пройдёт <u>:date_time</u>.<br><br>"
                    . "Всего участников: <strong>:count</strong><br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ],
        ],

        'site' => [
            'owner' => [
                'title' => 'Новая запись в ваш набор',
                'message' => 'Пользователь :client записался в ваш набор ":title_group". Пожалуйста, подтвердите или отклоните заявку.',
            ],

            'client' => [
                'title' => 'Вы записались в набор!',
                'message' => 'Вы успешно записались в набор ":title_group", который пройдёт :date_time.',
            ],
        ],


    ],

    // Отмена записи в набор (инициатор — клиент) //
    'cancel_user_on_group' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'date_time',
            'count',
            'url'
        ],

        'telegram' => [
            'owner' => [
                'message' => "😔 *Отмена записи.*\n\n"
                    . "👤 Пользователь :client отменил запись в вашем наборе:\n\n"
                    . "🏷 *Название набора:* :title_group\n"
                    . "📅 *Дата и время:*  :date_time\n"
                    . "👥 *Всего участников:* :count\n\n"
                    . "[Посмотреть детали набора](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ],

            'client' => [
                'message' => "Вы отменили запись в наборе\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "📅 *Дата и время:* :date_time \n"
                    . "[Посмотреть детали набора.](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'owner' => [
                'subject' => 'Пользователь отменил запись в наборе',
                'message' => "Здравствуйте, <strong>:owner</strong>!<br><br>"
                    . "<em>:client</em> отменил запись в вашем наборе '<strong>:title_group</strong>', который пройдёт <u>:date_time</u>.<br><br>"
                    . "Всего участников: <strong>:count</strong><br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ],

            'client' => [
                'subject' => 'Вы отменили запись в наборе',
                'message' => "Здравствуйте, <strong>:client</strong>!<br><br>"
                    . "Вы успешно отменили запись в набор '<strong>:title_group</strong>', который пройдёт <u>:date_time</u>.<br><br>"
                    . "Всего участников: <strong>:count</strong><br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ],
        ],

        'site' => [
            'owner' => [
                'title' => 'Запись отменена',
                'message' => 'Пользователь :client отменил запись в ваш набор ":title_group".',
            ],

            'client' => [
                'title' => 'Вы отменили запись',
                'message' => 'Вы успешно отменили запись в набор ":title_group".',
            ],
        ],
    ],

    // Заявка принята //
    'apply_register_user_on_group' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'date_time',
            'count',
            'url'
        ],

        'telegram' => [
            'client' => [
                'message' => "✅ *Ваша заявка принята!*\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "📅 *Дата и время:* :date_time \n"
                    . "👥 *Всего участников:* :count\n\n"
                    . "[Посмотреть детали набора.](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'client' => [
                'subject' => 'Ваша заявка была принята!',
                'message' => "Здравствуйте, <strong>:client</strong>!<br><br>"
                    . "Ваша заявка в набор '<strong>:title_group</strong>' была принята!<br>"
                    . "Занятия пройдут <u>:date_time</u><br><br>"
                    . "Всего участников: <strong>:count</strong><br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ]
        ],

        'site' => [
            'client' => [
                'title' => 'Ваша заявка была принята',
                'message' => 'Ваша заявка в набор ":title_group" была успешно подтверждена.',
            ],
        ]
    ],

    // Заявка отклонена //
    'reject_register_user_on_group' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'date_time',
            'count',
            'url'
        ],

        'telegram' => [
            'client' => [
                'message' => "❌ *Ваша заявка отклонена!*\n\n"
                    . "🏷 *Название набора:* [:title_group]\n\n"
                    . "[Посмотреть детали набора.](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'client' => [
                'subject' => 'Ваша заявка была отклонена!',
                'message' => "Здравствуйте, <strong>:client</strong>!<br><br>"
                    . "Ваша заявка в набор '<strong>:title_group</strong>' была отклонена.<br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ]
        ],

        'site' => [
            'client' => [
                'title' => 'Ваша заявка была отклонена',
                'message' => 'Ваша заявка в набор ":title_group" была отклонена.',
            ],
        ]
    ],

    // Запись вернули в заявки //
    'returned_register_user_on_group' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'date_time',
            'count',
            'url'
        ],

        'telegram' => [
            'client' => [
                'message' => "*Вашу запись вернули в заявки*\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "📅 *Дата и время:* :date_time \n"
                    . "👥 *Всего участников:* :count\n\n"
                    . "[Посмотреть детали набора.](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'client' => [
                'subject' => 'Вашу запись вернули в заявки',
                'message' => "Здравствуйте, <strong>:client</strong>!<br><br>"
                    . "Ваша запись в набор '<strong>:title_group</strong>' была возвращена в статус: Ожидает подтверждения.<br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ]
        ],

        'site' => [
            'client' => [
                'title' => 'Вашу запись вернули в заявки',
                'message' => 'Ваша запись в набор ":title_group" была возвращена в статус: Ожидает подтверждения.',
            ],
        ]
    ],

    // Добавление в избранное //
    'add_favorite' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'url'
        ],

        'telegram' => [
            'owner' => [
                'message' => "⭐ *Ваш набор добавили в избранное!*\n\n"
                    . "👤 Пользователь *:client* добавил ваш набор в избранное.\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "[Посмотреть детали набора](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'owner' => [
                'subject' => 'Ваш набор добавили в избранное',
                'message' => "Здравствуйте, <strong>:owner</strong>!<br><br>"
                    . "Пользователь <strong>:client</strong> добавил ваш набор '<strong>:title_group</strong>' в избранное.<br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ]
        ],

        'site' => [
            'owner' => [
                'title' => 'Ваш набор добавили в избранное',
                'message' => 'Пользователь :client добавил ваш набор ":title_group" в избранное.',
            ],
        ],
    ],

    // Удаление из избранного //
    'remove_favorite' => [
        'params' => [
            'owner',
            'client',
            'title_group',
            'url'
        ],

        'telegram' => [
            'owner' => [
                'message' => "💔 *Набор удалили из избранного*\n\n"
                    . "👤 Пользователь *:client* удалил ваш набор из избранного.\n\n"
                    . "🏷 *Название набора:* [:title_group]\n"
                    . "[Посмотреть детали набора](:url)"
                    . "\n\n📲 Если ссылка открывается внутри Telegram, нажмите в правом верхнем углу на иконку меню и выберите «Открыть в браузере»",
            ]
        ],

        'email' => [
            'owner' => [
                'subject' => 'Набор удалили из избранного',
                'message' => "Здравствуйте, <strong>:owner</strong>!<br><br>"
                    . "Пользователь <strong>:client</strong> удалил ваш набор '<strong>:title_group</strong>' из избранного.<br><br>"
                    . "<a href=\":url\" style=\"color: #007BFF; text-decoration: none;\">Открыть детали набора</a>",
            ]
        ],

        'site' => [
            'owner' => [
                'title' => 'Набор удалён из избранного',
                'message' => 'Пользователь :client удалил ваш набор ":title_group" из избранного.',
            ],
        ],
    ],

];

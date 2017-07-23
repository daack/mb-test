# mb-test

### Requirements

* Apache
* MySql
* Redis

### Api

```
POST /api/v1.0/sms

BODY {
    "recipient": "00395468321445",
    "originator": "MessageBird",
    "message": "message"
}

RESPONSE {
    "location": "/api/v1.0/sms/{id}"
}
```

```
GET /api/v1.0/sms/{id}

RESPONSE {
    "id": 1,
    "recipient": "00395468321445",
    "originator": "MessageBird",
    "message": "message",
    "chunks": "2",
    "udh_uid": "0A",
    "sent": "2",
    "created_at": "2017-07-23 17:50:06",
    "updated_at": "2017-07-23 17:50:06"
}
```

### MySql

```
CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `originator` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `chunks` int(11) DEFAULT '0',
  `udh_uid` varchar(2) DEFAULT NULL,
  `sent` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) CHARSET=utf8;
```

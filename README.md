# 勤怠アプリ

プロジェクト直下で、以下のコマンドを実行する。

```
make init
```

## メール認証
mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をしてください。<br>
https://mailtrap.io/

envに以下を追記。
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=MailtrapのUsername
MAIL_PASSWORD=MailtrapのPassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Laravel"


テストアカウント
一般ユーザー(スタッフ)
Email  staff@example.com
pass  password
管理者
Email  admin@example.com
pass   password


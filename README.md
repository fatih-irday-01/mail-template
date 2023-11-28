```
composer require fatihirday/mail-template
```

```
 php artisan vendor:publish --provider="Fatihirday\MailTemplate\MailTemplateServiceProvider"
```

<br />

#### MailTemplate
![img.png](img.png)

<br />

#### MailLanguage
![img_1.png](img_1.png)


<br />

---

`ExampleMail.php`

```[PHP]
<?php 

class ExampleMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    use MailViewTemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly string $languageCode = 'en',
        protected readonly array $data = []
    )
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->builder('default',  $this->languageCode);
    }
}
```


### Sender
```
$data = [
    'name' => 'fatih',
    'items' => ['a', 'b', 'c']
];

return Mail::to('fatihirday@gmail.com')
    ->send(new ExampleMail('tr', $data));
```

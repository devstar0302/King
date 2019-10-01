<div style="direction: <?php echo ($name['locale']  == 'he' ? 'rtl' : 'ltr') ?>">
    <p>{{__('Your URL')}}: {{ $name['login_url'] }}</p>
    <p>{{__('Your login')}}: {{ $name['login'] }}</p>
    <p>{{__('Your password')}}: {{ $name['password'] }}</p>
</div>


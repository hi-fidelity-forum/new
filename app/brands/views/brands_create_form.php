        <div class="create-brand-block">
            <form action="/<?=Request::initial()->controller();?>/quick_create" method="POST" class="createbrand_form">
                <input type="hidden" name="user_id" value="<?=$session->user()->get('uid');?>" />
                Создать бренд:&nbsp;<input type="text" value="" name="brand_name" class="brand_name" />
                <input type="submit" value="Создать" />
            </form>
        </div>
        <hr />
        
        
        <script type="text/javascript">
            $('.createbrand_form').submit(function(){
                var brand_name = $('.createbrand_form .brand_name').val();
                if (brand_name == '') {
                    alert('Пожалуйста укажите название Брэнда');
                    return false;
                }
            });
        </script>
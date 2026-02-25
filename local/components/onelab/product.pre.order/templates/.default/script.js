$(function() {
    $('.btn-onelab-pre-order-open').on('click', function() {
        $('.modal-onelab-pre-order').remove();

        var productId = $(this).data('product_id');

        $('body').append(`
            <div class="modal modal-onelab-pre-order" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Заказ товара</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label for="onelab-pre-order-name" class="form-label">Имя</label>
                                    <input type="text" class="form-control" name="NAME" id="onelab-pre-order-name" aria-describedby="">
                                </div>
                                <div class="mb-3">
                                    <label for="onelab-pre-order-phone" class="form-label">Телефон</label>
                                    <input type="text" class="form-control" name="PHONE"  id="onelab-pre-order-phone">
                                </div>
                                <div class="status"></div>
                                <div class="mt-6">
                                    <button type="button" class="btn btn-primary btn-send float-end">Отправить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `);

        var result = new BX.MaskedInput({
			mask: '+7 999 999 99 99',
			input: $('.modal-onelab-pre-order [name="PHONE"]')[0],
			placeholder: '_'
		});

        $('.modal-onelab-pre-order .btn-send').on('click', function() {
            var $form = $(this).closest('.modal-onelab-pre-order').find('form');
            var wait = BX.showWait($form[0]);

            $form.find('.status').html('');

            var data = $form.serializeArray();
            var dataSend = {};

            $.each(data, function() {
                dataSend[this.name] = this.value;
            });

            dataSend.PRODUCT_ID = productId;
    
            BX.ajax.runComponentAction('onelab:product.pre.order',
                'send', {
                mode: 'class',
                data: dataSend,
            })
            .then(function(response) {
                BX.closeWait($form[0], wait);
    
                if (response.status === 'success') {
                    $form.replaceWith('<p class="onelab-buy-one-click-success-send-text">Заявка принята.</p>');
                }
            })
            .catch((response) => {
                BX.closeWait($form[0], wait);
    
                var messages = [],
                    i = 0, ln = response.errors.length;
    
                for (; i < ln; i++) {
                    messages.push(response.errors[i].message);
                }
    
                $form.find('.status').html('<span class="error-text">' + messages.join('<br>') + '</span>');
            });
        });

        $('.modal-onelab-pre-order').modal('show');
    });
});
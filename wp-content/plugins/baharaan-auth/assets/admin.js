jQuery(function ($) {
  $('.bha-media-pick').on('click', function (event) {
    event.preventDefault();
    var wrap = $(this).closest('.bha-media');
    var frame = wp.media({ title: 'انتخاب تصویر لوازم خانگی', button: { text: 'استفاده از این تصویر' }, multiple: false });
    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      wrap.find('input').val(attachment.id);
      wrap.find('img').attr('src', attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).prop('hidden', false);
    });
    frame.open();
  });
  $('.bha-media-clear').on('click', function (event) {
    event.preventDefault();
    var wrap = $(this).closest('.bha-media');
    wrap.find('input').val('0');
    wrap.find('img').prop('hidden', true).attr('src', '');
  });
});

(function ($) {
  'use strict';
  $('#snd-select-image').on('click', function () {
    const picker = wp.media({ title: 'Fotografija znamenitosti', library: { type: 'image' }, multiple: false });
    picker.on('select', function () { $('#snd-landmark_image').val(picker.state().get('selection').first().toJSON().url); });
    picker.open();
  });
  $('#snd-filter').on('input', function () {
    const query = this.value.toLocaleLowerCase('sr');
    $('#snd-territories tr').each(function () { this.hidden = !this.textContent.toLocaleLowerCase('sr').includes(query); });
  });
})(jQuery);

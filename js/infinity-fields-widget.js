function ifw_add_field(el) {
    var $ = jQuery;
    var element = $(el)
    var ifw = element.parent().parent().parent();

    var widget_base = ifw.find('.id_base').val();
    var widget_number = ifw.find('.multi_number').val();
    if (widget_number.length === 0) {
        widget_number = ifw.find('.widget_number').val();
    }
    var rand_ = 'xxxxxxxyxxxx4xxxyxxxxxxxxxxxxxifw'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    var name = 'widget-' + widget_base + '[' + widget_number + '][' + rand_ + ']';
    var id = 'widget-' + widget_base + '-' + widget_number + '-' + rand_;

    var box = ifw.find('.ifw');
    var clone = box.find('div:last-of-type').eq(0).clone();
    var index = box.find('div').length + 1;
    var i = 0;
    clone.find('input').each(function () {
        $(this).attr('name', name.replace('ifw]', i + ']')).attr('id', id + i).val('');
        $(this).parent().find('label').attr('for', id + i);
        i++;
    });
    clone.find('.ifw-label').html(index);
    clone.insertBefore(element);
    
    return false;
}
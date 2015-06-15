/**
 * Created by NyanTun on 1/21/2015.
 */
$(function () {
    $('.sundew-tree li:has(ul)').addClass('parent_li').find(' > div.tree-item').attr('title', 'Collapse');
    $('.sundew-tree li.parent_li > div.tree-item > span#icon').on('click', function (e) {
        var children = $(this).parent('div.tree-item').parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand').addClass('glyphicon-folder-close').removeClass('glyphicon-folder-open');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
        }
        e.stopPropagation();
    });
});
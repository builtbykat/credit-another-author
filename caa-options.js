jQuery(document).ready(function($) {
    if (author_class.val != '' && typeof other_author != 'undefined') {
        var prefix = '.post-'+other_author.val['id']+' ';
        $(prefix + author_class.val['author-class']).html(other_author.val['name']);
        $(prefix + author_class.val['author-class']).attr('href', other_author.val['url']);
        $(prefix + author_class.val['img-class']).attr('src', other_author.val['img']);
        $(prefix + author_class.val['img-class']).attr('srcset', other_author.val['img']);
    }
})
function delete_confirm(question,id,ordem) {
    console.log(ordem);
    if (confirm(question)) {
        location.href = 'scripts/ordem-delete/ordem-delete.php?ordem='+ordem+'&id='+id;
    } else {
        return false;
    }
}
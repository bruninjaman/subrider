function delete_confirm(question,id,ordem) {
    console.log(ordem);
    if (confirm(question)) {
        location.href = baseAddress + '/scripts/ordem-delete/ordem-delete.php?ordem='+ordem+'&id='+id;
    } else {
        return false;
    }
}
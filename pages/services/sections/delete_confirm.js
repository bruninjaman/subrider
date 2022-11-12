function delete_confirm(question,id,ordem) {
    console.log(ordem);
    if (confirm(question)) {
        location.href = 'scripts/deleteservices/delete-service.php?ordem='+ordem+'&id='+id;
    } else {
        return false;
    }
}
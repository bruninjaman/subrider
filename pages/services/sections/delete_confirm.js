function delete_confirm(question,type,id,ordem) {
    if (confirm(question)) {
        location.href = 'scripts/deleteservices/delete-service.php?type='+type+"&id="+id+"&ordem="+ordem;
    } else {
        return false;
    }
}
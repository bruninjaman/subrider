<tr>
    <td class="foto"><img src="<?php echo $row["foto"] ?>" style="height:100px;width:100px;"></td>
    <td class="grupo"><?php echo $row["grupo"]?></td>
    <td class="item"><?php echo $row["item"]?></td>
    <td class="parte"><?php echo $row["parte"]?></td>
    <td class="hidden pecaId"><?php echo $row["pecaId"]?></td>
    <td>
        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
    </td>
</tr>
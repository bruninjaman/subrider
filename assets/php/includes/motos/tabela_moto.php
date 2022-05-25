<tr>
    </td>
    <!--<td><img src="https://static.thenounproject.com/png/3674270-200.png" style="height:100px;width:100px;"></td> -->
    <td class="foto"><img src="<?php echo $row["foto"] ?>" style="height:100px;width:100px;"></td>
    <td class="prop"><?php echo $row["proprietario"]?></td>
    <td class="ender"><?php echo $row["endereco"]?></td>
    <td class="marca"><?php echo $row["marca"]?></td>
    <td class="placa"><?php echo $row["placa"]?></td>
    <td class="ano"><?php echo $row["ano"]?></td>
    <td class="model"><?php echo $row["modelo"]?></td>
    <td class="km"><?php echo $row["km"]?></td>
    <td class="motoId hidden"><?php echo $row["motoId"]?></td>
    <td>
        <a href="#" class="services" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Mostrar Ordens de Serviço">&#xe0e0;</i></a>
        <a href="#" class="sync" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Adicionar/trocar Proprietario">&#xe7ef;</i></a>
        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
    </td>
</tr>
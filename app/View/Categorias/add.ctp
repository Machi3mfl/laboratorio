<div class="col-md-2">
    <legend>
        <h3><?php echo __('Acciones'); ?></h3>
    </legend>
    <ul class="nav nav-colapse">
            <li><?php echo $this->Html->link(__('Listar Categorias'), array('action' => 'index')); ?></li>
            <li><hr></li>
            <li><?php echo $this->Html->link(__('Listar Productos'), array('controller' => 'productos', 'action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('Nuevo Producto'), array('controller' => 'productos', 'action' => 'add')); ?> </li>
    </ul>
</div>
<div class="col-md-9">
    <legend><?php echo __('Añadir nueva Categoria'); ?></legend>
        <?php echo $this->Form->create('Categoria', array('data-toggle' => 'validator' , 'role' => 'form')); ?>
        <div class="col-md-5">
        <div class="form-group">
            <?php
                echo $this->Form->input('nombre',array(
                  'div' => false , 'class' => 'form-control' , 'required' => true , 'type' => 'text' , 'after' => '<div class="help-block with-errors"></div>'));
            ?>
        </div>
        <div class="form-group clear"></div>
        <?php echo $this->Form->submit(__('Agregar'), array('class' => 'btn btn-primary')); ?>
        <?php echo $this->Form->end();?>
        </div>
</div>

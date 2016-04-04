<?php
App::uses('AppController', 'Controller');
App::uses('ClientesController', 'Controller');
App::uses('CopiasController','Controller');
/**
 * Pedidos Controller
 *
 * @property Pedido $Pedido
 */
class PedidosController extends AppController {

/**
 * index method
 *
 * @return void
 */
    public function index() {
            $this->Pedido->recursive = 0;
            $this->set('pedidos', $this->paginate());
    }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
      $this->loadModel("Upload");
      $this->Pedido->id = $id;
      $uploads=array();
      if (!$this->Pedido->exists()) {
              throw new NotFoundException(__('Invalid pedido'));
      }
      $pedido=$this->Pedido->read(null, $id);

      foreach( $pedido['Copia'] as $copia){
        $upload=$this->Upload->read(null,$copia['upload_id']);
        array_push($uploads,$upload);
      }
      $this->set('pedido', $this->Pedido->read(null, $id));
      //debug($uploads);
      $this->set('uploads',$uploads);
    }

/**
 * add method
 *
 * @return void
 */
    public function add2() {
        $this->loadModel('Upload');
        $usuario=$this->Auth->user();
        $guardados=array();
        // PARA DESPUES : GUARDAR FILES TEMPORALES Y LUEGO GUARDAR EN BD - USAR TMP EN FORMULARIO
        //$this->Session->delete('imagenes');
        if (!empty($this->request->data)){
            $files=array();
            foreach($this->request->data["Upload"]["photo"] as $file) { // por cada photo setea un file
                $this->Upload->set(array('photo' => $file));
                $photo = $this->Upload->data;
                array_push($files,$photo);
            } /**** fin foreach ***/
            if (!empty($this->Session->read('files'))){ //lee las imagenes de la session, las apila y luego guarda
                $ant = $this->Session->read('files');
                foreach($files as $img) {
                  array_push($ant, $img);
                }
                $this->Session->write('files',$ant);
                $this->set('files',$ant);
                $this->set('imgs',$ant);
                $this->set('cantidad',count($ant));
            }
            else{ // si no hay imagenes en la session solo guarda
                $this->Session->write('files',$files);
                $this->set('files',$files);
                $this->set('imgs',$files);
            }
        $this->setearModelos();
        //debug($this->Session->read('files'));//$this->set('uploads',$guardados);
        }else{
            if($this->Session->check('files')){
                $img = $this->Session->read('files');
                $this->set('files',$img);
                $this->set('imgs',$img);
                $this->set('cantidad',count($img));
                $this->setearModelos();
            }
        }

    }

    public function setearModelos() {
      $categorias=$this->Upload->Copia->Producto->Categoria->find('list');
      $superficies=null;
      $tamanos=null;
      $this->set(compact('superficies','tamanos','categorias'));
    }

    /*
     * public
     *
     * obtiene los ultimos uploads por ID
     *
     * return array de Uploads
     */
    public function listarGuardados(array $guardados){
        if(!empty($guardados)){
            foreach($guardados as $id){
                $result[]=$this->Upload->findById($id);
            }
            return $result;
        }
    }
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        $this->Pedido->id = $id;
        if (!$this->Pedido->exists()) {
          throw new NotFoundException(__('Invalid pedido'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
          if ($this->Pedido->save($this->request->data)) {
                  $this->Session->setFlash(__('The pedido has been saved'));
                  $this->redirect(array('action' => 'index'));
          } else {
                  $this->Session->setFlash(__('The pedido could not be saved. Please, try again.'));
          }
        } else {
          $this->request->data = $this->Pedido->read(null, $id);
        }
        $clientes = $this->Pedido->Cliente->find('list');
        $this->set(compact('clientes'));
    }

/**
* delete method
*
* @throws MethodNotAllowedException
* @throws NotFoundException
* @param string $id
* @return void
*/
    public function delete($id = null) {
        if (!$this->request->is('post')) {
                throw new MethodNotAllowedException();
        }
        $this->Pedido->id = $id;
        if (!$this->Pedido->exists()) {
                throw new NotFoundException(__('Invalid pedido'));
        }
        if ($this->Pedido->delete()) {
                $this->Session->setFlash(__('Pedido deleted'));
                $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Pedido was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

    public function add(){
      $this->loadModel('Upload');
      $usuario=$this->Auth->user();
      $guardados=array();
      $files=array(); // PARA DESPUES : GUARDAR FILES TEMPORALES Y LUEGO GUARDAR EN BD - USAR TMP EN FORMULARIO
      //$this->Session->delete('imagenes');
      if (!empty($this->request->data)){ // si hay imagenes
          foreach($this->request->data["Upload"]["photo"] as $file) { // por cada photo setea un file
              $this->Upload->set(array('photo' => $file));
              $photo = $this->Upload->data;
              array_push($files,$photo);
              $this->Upload->create();
              if ($this->Upload->save($photo)) { // guarda el file en BD
                  $ultimo = $this->Upload->getLastInsertId(); //obtiene el ultimo id , para crear una pila
                  array_push($guardados,$ultimo); //guarda en array los id files guardados
              }
          } /**** fin foreach ***/
          $imgs=$this->listarGuardados($guardados);
          if (!empty($this->Session->read('imagenes'))){ //lee las imagenes de la session, las apila y luego guarda
              $ant = $this->Session->read('imagenes');
              foreach($imgs as $img) {
                  array_push($ant, $img);
              }
              $this->Session->write('imagenes',$ant);
              $this->set('imgs',$ant);
              $this->set('cantidad',count($ant));
          }
          else{ // si no hay imagenes en la session solo guarda
              $this->Session->write('imagenes',$imgs);
              $this->set('imgs',$imgs);
          }
      $this->setearModelos();
      $this->set('uploads',$guardados);
      }else{
          if($this->Session->check('imagenes')){
              $img = $this->Session->read('imagenes');
              $this->set('imgs',$img);
              $this->set('cantidad',count($img));
              $this->setearModelos();
          }
      }

    }



    public function confirmar(){
      $pedido=$this->request->data["Pedido"];
      $copias=$this->request->data["Upload"]["Copias"];
      $uploads=$this->Session->read('imagenes');
      $Copias= new CopiasController;

      if(!empty($pedido)){
        $pedido["fecha"]=date("Y-m-d H:i:s");//fecha del pedido
        $this->Pedido->save($pedido);
        $id= $this->Pedido->getLastInsertId();
        $Copias->guardarCopias($copias,$id);
        $this->Session->setFlash("Pedido guardado");
        $this->Session->delete('imagenes');
      }else{
        $this->Session->setFlash("No se ha podido guardar el pedido");
      }

    }

    public function testUpload(){
    $this->loadModel("Upload");
    $pila=array();
      if(!empty($this->request->data)){
        foreach($this->request->data["Upload"]["photo"]  as $index => $foto){
          $this->Upload->set(array("photo" => $foto));
          $upload=$this->Upload->data;
          array_push($pila,$upload);
          /*$this->Upload->create();
          if($this->Upload->save($upload)){
            $this->Session->setFlash("Archivo ".$index." guardado");
          }
          else{
            $this->Session->getFlash("Archivo ".$index." NO SE HA GUARDADO");
          }*/
        }
        $this->Session->write("files",$pila);
      }
      else{
        debug("request data vacio");
      }
    }

    public function guardar(){
    $this->loadModel("Upload");
    $files=array();
    if($this->Session->check("files")){
      $files=$this->Session->read("files");
      foreach($files as $file){
        $this->Upload->set(array("photo" => $file["Upload"]["photo"]));
        $arch=$this->Upload->data;
        debug($arch["Upload"]);
        $this->Upload->create();
        /*if($this->Upload->save($arch["Upload"])){
          $this->Session->setFlash("Archivo guardado.");
        }else{
          $this->Session->setFlash("Archivo falló.");
        }*/
        }
      }else{
        $this->Session->setFlash("Session vacio.");
      }

      //rename(C:\wamp\tmp\php203A.tmp,C:\wamp\www\laboratorio\app\webroot\files\uploads\232\maximilianoibarra-perfil.jpg);
    }


}

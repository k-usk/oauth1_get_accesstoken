<?php
$current_url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

if(array_key_exists('action', $_GET)){
  if($_GET['action'] == 'token'){
    if(array_key_exists('oauth_verifier', $_POST)){
      if(strlen($_POST['oauth_verifier']) == 24){
        $redirect_url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
        $redirect_url = $redirect_url.'?page='.$_GET['page'].'&action=done&oauth_verifier='.$_POST['oauth_verifier'];
      }else{
        $this->isErr = true;
        $this->err = 'Verification tokenを正しく入力して下さい';
      }
    }else{
      //エラー
      $this->isErr = true;
      $this->err = 'Verification tokenを入力して下さい';
    }
?>
<script type="text/javascript">
  location.href = '<?php echo $redirect_url; ?>';
</script>
<?php
  }
}

 ?>

 <?php
if ( $this->isErr ) { ?>
  <div class="error"><p><strong><?php esc_html_e($this->err) ?></strong></p></div>
<?php } ?>

<div class="wrap">
<h2>Get AccessToken</h2>
</div>

 <div class="wrap">
   <h3>1. Get verification token</h3>
   <form method="post" action="<?php echo $current_url.'&action=verify' ?>">

     <table class="form-table">
      <tbody>
        <tr>
         <th scope="row">Client Key</th>
         <td>
           <input type="text" id="message" name="client_key" class="regular-text" />
         </td>
        </tr>
        <tr>
         <th scope="row">Client Secret</th>
         <td>
           <input type="text" id="message" name="client_secret" class="regular-text" />
         </td>
        </tr>
      </tbody>
     </table>
   <?php submit_button('submit'); ?>
   </form>
 </div><!-- .wrap -->


 <div class="wrap">
   <h3>2. Get Access token</h3>
   <form method="post" action="<?php echo $current_url.'&action=token' ?>">
     <table class="form-table">
      <tbody>
        <tr>
         <th scope="row">Verification token</th>
         <td>
           <input type="text" id="message" name="oauth_verifier" class="regular-text" />
         </td>
        </tr>
      </tbody>
     </table>
   <?php submit_button('submit'); ?>
   </form>
 </div><!-- .wrap -->

 <?php if(!is_null($this->access_token)): ?>
<div class="wrap">
  <h3>Access token</h3>
  <table class="form-table">
    <tbody>
      <tr>
       <th scope="row">OAuth Token</th>
       <td><code><?php echo $this->access_token["oauth_token"] ?></code></td>
      </tr>
      <tr>
       <th scope="row">	OAuth Token Secret</th>
       <td><code><?php echo $this->access_token["oauth_token_secret"] ?></code></td>
      </tr>
    </tbody>
  </table>
</div>
 <?php endif; ?>

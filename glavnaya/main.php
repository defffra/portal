<main>
<nav>

<a href="#openModal" class="avtor"><b>Авторизация</a>
<a href="#openModalreg"><b>Регистрация</a>
    </nav>


<div class="foto2"></div>
<div class="block1">


<div class="text1">
<h2>Ямочный ремонт дорог</h2>
<p>Ремонт дороги</p>
<h3>10.06.2023</h3>
</div>
</div>



<div class="foto3"></div>
<div class="block2">


<div class="text2">
<h2>Ремонт детской площадки</h2>
<p>Ремонт площадки</p>
<h3>10.06.2023</h3>
</div>


</div>
<div class="foto4"></div>
<div class="block3">

<div class="text3">
<h2>Реконструкция здания</h2>
<p>Замена фасада</p>
<h3>10.06.2023</h3>
</div>

</div>

<div class="block4">
    <h1>Количество решенных заявок:</h1>
    <div class="chet"></div>
</div>
    
<script>
  const min = 10;
const max = 1000;
const time = 2000000;

(function step(val) {
  setTimeout(function() {
    document.querySelector('.chet').innerText = val;
    if (val < max) {
      step(val + 1);
    }
  }, time / (max - min));
})(min);
</script>


<div id="openModal" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">АВТОРИЗАЦИЯ</h3>
        <a href="#close" title="Close" class="close">×</a>
      </div>
      <form action="templates/avtor.php" method="POST">
      <div class="modal-body">    
       
      <input type="text" class="loginavtor" name="login" placeholder="Логин" required ><br>
      <input type="password" class="passwordavtor" name="password"placeholder="Пароль" required>
</div>

<div class="cd-user-modal"> 
 <div class="cd-user-modal-container"> 
      <ul class="cd-switcher">
      <li><input type="submit" value="Sign In" class="signn"></li>
      <li><a href="#openModalreg" class="reg">Register</a></li>
      </ul>
      </div>
 </div>
</div>
    </div>
    </form>
  </div>

<div id="openModalreg" class="modal">
  <div class="modal-dialog-reg">
    <div class="modal-content-reg">
      <div class="modal-header-reg">
        <h3 class="modal-title-reg">РЕГИСТРАЦИЯ</h3>
        <a href="#close" title="Close" class="close">×</a>
      </div>
      <form action="templates/reg.php" method="POST">
      <div class="modal-body-reg">    
       
      <input type="text" class="fioreg" name="fio" placeholder="ФИО" required><br>
      <input type="email" class="emailreg" name="email" placeholder="E-mail" required><br>
      <input type="text" class="loginreg" name="login" placeholder="Логин" required><br>
      <input type="password" class="passwordreg" name="password" placeholder="Пароль" required><br>
      <input type="password" name="confirm_password" placeholder="Повтор пароля"  required>
      <input type="checkbox" required class='consent' name="consent" required><p>Согласие на обработку данных</p>
      </div>

      <div class="cd-user-modal-reg"> 
 <div class="cd-user-modal-container-reg"> 
      <ul class="cd-switcher-reg">
      <li><a href="#openModal" class="signn-reg">Sign In</a></li>
      <li> <input type="submit" value="Register" class="register"></li>
      </ul>
      </div>
 </div>
</div>
    </div>
    </form>
  </div>
  </main>




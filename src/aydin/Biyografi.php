<?php

namespace aydin;

use pocketmine\{
  player\Player,
  Server,
  utils\Config,
  plugin\PluginBase,
  command\Command,
  command\CommandSender,
  event\Listener,
  event\player\PlayerJoinEvent,
};
use jojoe77777\FormAPI\{
  SimpleForm,
  CustomForm,
};

class Biyografi extends PluginBase implements Listener{
  public static $cfg;
  public function onEnable():void{
    $this->getLogger()->info("Biyografi aktif");
    self::$cfg = new Config($this->getDataFolder(). "biyografiler.yml", Config::YAML);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
      public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(!self::$cfg->get($player->getName())){
      self::$cfg->set($player->getName(), "<3");
      self::$cfg->save();
    }
  }
  public function onCommand(CommandSender $player, Command $kmt, string $lbl, array $args):bool{
    
    if($kmt->getName() == "biyografi"){
      if($player instanceof Player){
        $this->bio($player);
      }else{
        $player->sendMessage("§cBu komut oyunda kullanılabilir");
      }
    }
    return true;
  }
  public function bio(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
      if($data === null) return true;
      switch($data){
        case 0:
          $this->mybio($player);
          break;
          case 1:
            $this->biosu($player);
            break;
      }
    });
    $form->setTitle("Biyografi Menüsü");
    $form->setContent("\n§7Biyografiniz: ".self::$cfg->get($player->getName())."\n");
    $form->addButton("Biyografini Güncelle");
    $form->addButton("Başkasının Biyografisine Bak");
    $form->sendToPlayer($player);
  }
  public function mybio(Player $player){
    $form = new CustomForm(function(Player $player, $data){
      
      if($data === null) return true;
      if($data[1] != null && $data[1] != "" && $data[1] != " " && $data[1] != "  " && $data[1] != "   "){
        
        if($data[1] != "§"){
          
          $kisi = self::$cfg->get($player->getName());
          
          $biomsj = $data[1];
          
          self::$cfg->set($player->getName(), $biomsj);
          
          self::$cfg->save();
          
          $player->sendMessage("§2 » §aBiyografiniz §2$data[1]§a olarak değiştirildi.");
        }else{
          $player->sendMessage("§4 » §cBiyografinde Sembol kullanamazsın");
        }
      }else{
        $player->sendMessage("§4 » §cBiyografini boş bırakma!");
      }
    });
    $bio = self::$cfg->get($player->getName());
    $form->setTitle("Biyografi Menüsü");
    $form->addLabel("\n§7Bu menüden sunucu biyografinizi güncelleyebilirsiniz.\n");
    $form->addInput("\n\n§7Biyografi gir", "<3", "$bio");
    $form->sendToPlayer($player);
  }
  
  public function biosu(Player $player){
    
    $list = []; foreach($this->getServer()->getOnlinePlayers() as $o){
          $list[] = $o->getName();
          $playerList = $list;
    }
    $form = new CustomForm(function(Player $player, $data) use ($list, $playerList){
      if($data === null) return true;
          $oyuncu = $this->getServer()->getPlayerExact($playerList[$data[1]]);
          $biosus = self::$cfg->get($oyuncu->getName());
          $this->biosuform($player, $oyuncu);
    });
    $form->setTitle("Başka Oyuncunun Biyografisine Bakma Menüsü");
    $form->addLabel("\n\n§7Biyografisine bakacağın oyuncuyu seç\n");
    $form->addDropDown("§7Oyuncu seç", $list);
    $form->sendToPlayer($player);
  }
  
  public function biosuform(Player $player, $oyuncu){
    $form = new CustomForm(function(Player $player, $data) use ($oyuncu){
      if($data === null) return true;
    });
    $o = $oyuncu->getName();
    $bio = self::$cfg->get($oyuncu->getName());
    $form->setTitle("$o");
    $form->addLabel("§7\n\n§f - $o §3adlı oyuncun biyografisi:\n§f - §3$bio");
    $form->sendToPlayer($player);
  }
}

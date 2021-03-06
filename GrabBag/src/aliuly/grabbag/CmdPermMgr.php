<?php
/**
 ** OVERVIEW:Player Management
 **
 ** COMMANDS
 **
 ** * perm : temporarily change player's permissions
 **   usage: **perm** _<player>_ _<dump|permission> _[true|false]_
 **
 **   This can be used to temporarily change player's permissions.
 **   Changes are only done in-memory, so these will revert if the
 **   disconnects or the server reloads.
 **   You can specify a _permission_ and it will show it's valueor
 **   if true|false is specified it will be changed.
 **   If you specify **dump**, it will show all permissions
 **   associated to a player.
 **
 **/

namespace aliuly\grabbag;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

use aliuly\grabbag\common\BasicCli;
use aliuly\grabbag\common\mc;

class CmdPermMgr extends BasicCli implements CommandExecutor {
	public function __construct($owner) {
		parent::__construct($owner);
		$this->enableCmd("perm",
							  ["description" => mc::_("change permissions"),
								"usage" => mc::_("/perm <player> <dump|permission> [true|false]"),
								"permission" => "gb.cmd.permmgr"]);
	}
	public function onCommand(CommandSender $sender,Command $cmd,$label, array $args) {
		if ($cmd->getName() != "perm") return false;
		$pageNumber = $this->getPageNumber($args);
		if (count($args) < 2) return false;

		$target = $this->owner->getServer()->getPlayer($args[0]);
		if ($target == null) {
			$sender->sendMessage(TextFormat::RED.mc::_("%1%: Not found",$args[0]));
			return true;
		}
		array_shift($args);
		if (strtolower($args[0]) == "dump") {
			if (count($args) != 1) return false;
			$txt = [ TextFormat::YELLOW.mc::_("Permissions for %1%", $target->getName()) ];
			foreach ($target->getEffectivePermissions() as $pp) {

				$txt[] = TextFormat::GREEN.$pp->getPermission() .": ".
						 TextFormat::WHITE.($pp->getValue()
												  ? mc::_("YES") : mc::_("NO"));
			}
			return $this->paginateText($sender,$pageNumber,$txt);
		}
		$perm = array_shift($args);
		if (count($args) > 1) return false;
		if (count($args) == 1) {
			$bool = filter_var(array_shift($args), FILTER_VALIDATE_BOOLEAN);
			$target->addAttachment($this->owner,$perm,$bool);
		}
		$sender->sendMessage(TextFormat::YELLOW.$target->getName().",".
									TextFormat::GREEN.$perm.": ".
									TextFormat::WHITE.($target->hasPermission($perm)
															 ? mc::_("YES") : mc::_("NO")));
		return true;
	}
}

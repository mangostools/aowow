<?php

/*
 * UDWBase: WOWDB Web Interface
 *
 * © UDW 2009-2011
 *
 * Released under the terms and conditions of the
 * GNU General Public License (http://gnu.org).
 *
 */

/**
 *
 * @param type $d
 * @param type $v 
 */
function d($d, $v) {
    define($d, $v);
}

switch ($_SESSION['locale']) {
    // --- ENGLISH default LOCALE ---
    default:
        // quest
        d('LOCALE_REQUIREMENTS', 'Requirements');
        // classes
        d('LOCALE_WARRIOR', 'Warrior');
        d('LOCALE_PALADIN', 'Paladin');
        d('LOCALE_HUNTER', 'Hunter');
        d('LOCALE_ROGUE', 'Rogue');
        d('LOCALE_PRIEST', 'Priest');
        d('LOCALE_SHAMAN', 'Shaman');
        d('LOCALE_MAGE', 'Mage');
        d('LOCALE_WARLOCK', 'Warlock');
        d('LOCALE_DRUID', 'Druid');
        // races
        d('LOCALE_HUMAN', 'Human');
        d('LOCALE_ORC', 'Orc');
        d('LOCALE_DWARF', 'Dwarf');
        d('LOCALE_NIGHT_ELF', 'Night Elf');
        d('LOCALE_UNDED', 'Undead');
        d('LOCALE_TAUREN', 'Tauren');
        d('LOCALE_GNOME', 'Gnome');
        d('LOCALE_TROLL', 'Troll');
        // side
        d('LOCALE_BOTH', 'Both');
        d('LOCALE_HORDE', 'Horde');
        d('LOCALE_ALLIANCE', 'Alliance');
        // reputation
        d('LOCALE_NEUTRAL', 'Neutral');
        d('LOCALE_FRIENDLY', 'Friendly');
        d('LOCALE_HONORED', 'Honored');
        d('LOCALE_REVERED', 'Revered');
        d('LOCALE_EXALTED', 'Exalted');
        // resistances
        d('LOCALE_FIRE_RESISTANCE', 'Fire Resistance');
        d('LOCALE_FROST_RESISTANCE', 'Frost Resistance');
        d('LOCALE_ARCANE_RESISTANCE', 'Arcane Resistance');
        d('LOCALE_SHADOW_RESISTANCE', 'Shadow Resistance');
        d('LOCALE_NATURE_RESISTANCE', 'Nature Resistance');
        d('LOCALE_HOLY_RESISTANCE', 'RESISTANCE DOES NOT EXIST');
        // binds
        d('LOCALE_BIND_PICKUP', 'Binds when picked up');
        d('LOCALE_BIND_EQUIP', 'Binds when equipped');
        d('LOCALE_BIND_USE', 'Binds when used');
        d('LOCALE_BIND_SOULBOUND', 'Soulbound');
        d('LOCALE_BIND_QUEST_ITEM', 'Quest Item');
        // bags
        d('LOCALE_BAG', 'Bag');
        d('LOCALE_BAG_QUIVER', 'Quiver');
        d('LOCALE_BAG_AMMO', 'Ammo Pouch');
        d('LOCALE_BAG_SOUL', 'Soul Bag');
        d('LOCALE_BAG_LEATHER', 'Leatherworking Bag');
        d('LOCALE_BAG_HERB', 'Herb Bag');
        d('LOCALE_BAG_ENCHANT', 'Enchanting bag');
        d('LOCALE_BAG_ENGINEER', 'Engineering Bag');
        d('LOCALE_BAG_MINING', 'Mining Bag');
        // equip slots
        d('LOCALE_EQUIP_HEAD', 'Head');
        d('LOCALE_EQUIP_NECK', 'Neck');
        d('LOCALE_EQUIP_SHOULDER', 'Shoulder');
        d('LOCALE_EQUIP_SHIRT', 'Shirt');
        d('LOCALE_EQUIP_CHEST', 'Chest');
        d('LOCALE_EQUIP_WAIST', 'Waist');
        d('LOCALE_EQUIP_LEGS', 'Legs');
        d('LOCALE_EQUIP_FEET', 'Feet');
        d('LOCALE_EQUIP_WRIST', 'Wrist');
        d('LOCALE_EQUIP_HANDS', 'Hands');
        d('LOCALE_EQUIP_FINGER', 'Finger');
        d('LOCALE_EQUIP_TRINKET', 'Trinket');
        d('LOCALE_EQUIP_ONEHAND', 'One-hand');
        d('LOCALE_EQUIP_OFFHAND', 'Off Hand');
        d('LOCALE_EQUIP_RANGED', 'Ranged');
        d('LOCALE_EQUIP_BACK', 'Back');
        d('LOCALE_EQUIP_TWOHAND', 'Two-hand');
        d('LOCALE_EQUIP_UNK0', '');
        d('LOCALE_EQUIP_TABARD', 'Tabard');
        d('LOCALE_EQUIP_MAINHAND', 'Main Hand');
        d('LOCALE_EQUIP_CHEST2', 'Chest');
        d('LOCALE_EQUIP_OFFHAND2', 'Off Hand');
        d('LOCALE_EQUIP_HELDINOFFHAND', 'Held In Off-Hand');
        d('LOCALE_EQUIP_PROJECTILE', 'Projectile');
        d('LOCALE_EQUIP_THROWN', 'Thrown');
        d('LOCALE_EQUIP_RANGED2', 'Ranged');
        d('LOCALE_EQUIP_UNK1', '');
        d('LOCALE_EQUIP_RELIC', 'Relic');
        // armor type
        d('LOCALE_ARMOR_CLOTH', 'Cloth');
        d('LOCALE_ARMOR_LEATHER', 'Leather');
        d('LOCALE_ARMOR_MAIL', 'Mail');
        d('LOCALE_ARMOR_PLATE', 'Plate');
        d('LOCALE_ARMOR_BUCKLER', '');
        d('LOCALE_ARMOR_SHIELD', 'Shield');
        d('LOCALE_ARMOR_LIBRAM', 'Libram');
        d('LOCALE_ARMOR_IDOL', 'Idol');
        d('LOCALE_ARMOR_TOTEM', 'Totem');
        // weapon type
        d('LOCALE_WEAPON_AXE1H', 'Axe');
        d('LOCALE_WEAPON_AXE2H', 'Axe');
        d('LOCALE_WEAPON_BOW', 'Bow');
        d('LOCALE_WEAPON_GUN', 'Gun');
        d('LOCALE_WEAPON_MACE1H', 'Mace');
        d('LOCALE_WEAPON_MACE2H', 'Mace');
        d('LOCALE_WEAPON_POLEARM', 'Polearm');
        d('LOCALE_WEAPON_SWORD1H', 'Sword');
        d('LOCALE_WEAPON_SWORD2H', 'Sword');
        d('LOCALE_WEAPON_OBSOLETE', '');
        d('LOCALE_WEAPON_STAFF', 'Staff');
        d('LOCALE_WEAPON_EXOTIC', '');
        d('LOCALE_WEAPON_EXOTIC2', '');
        d('LOCALE_WEAPON_FIST', 'Fist Weapon');
        d('LOCALE_WEAPON_MISC', 'Miscellaneous');
        d('LOCALE_WEAPON_DAGGER', 'Dagger');
        d('LOCALE_WEAPON_THROWN', 'Thrown');
        d('LOCALE_WEAPON_SPEAR', '');
        d('LOCALE_WEAPON_CROSSBOW', 'Crossbow');
        d('LOCALE_WEAPON_WAND', 'Wand');
        d('LOCALE_WEAPON_FISHINGPOLE', 'Fishing Pole');
        // projectile type
        d('LOCALE_PROJECTILE_WAND', '');
        d('LOCALE_PROJECTILE_BOLT', '');
        d('LOCALE_PROJECTILE_ARROW', 'Arrow');
        d('LOCALE_PROJECTILE_BULLET', 'Bullet');
        d('LOCALE_PROJECTILE_THROWN', '');
        // damage
        d('LOCALE_DAMAGE_PRE', ' ');
        d('LOCALE_DAMAGE_POST', ' Damage');
        d('LOCALE_DAMAGE_HOLY', 'Holy');
        d('LOCALE_DAMAGE_FIRE', 'Fire');
        d('LOCALE_DAMAGE_FROST', 'Frost');
        d('LOCALE_DAMAGE_ARCANE', 'Arcane');
        d('LOCALE_DAMAGE_SHADOW', 'Shadow');
        d('LOCALE_DAMAGE_NATURE', 'Nature');
        // stats
        d('LOCALE_STAT_STRENGTH', ' Strength');
        d('LOCALE_STAT_STAMINA', ' Stamina');
        d('LOCALE_STAT_INTELLECT', ' Intellect');
        d('LOCALE_STAT_SPIRIT', ' Spirit');
        d('LOCALE_STAT_AGILITY', ' Agility');
        // green bonuses
        d('LOCALE_GBONUS_DEFENCE', 'Increases defense rating by %d.');
        d('LOCALE_GBONUS_DODGE', 'Increases your dodge rating by %d.');
        d('LOCALE_GBONUS_PARRY', 'Increases your parry rating by %d.');
        d('LOCALE_GBONUS_SHIELDBLOCK', 'Increases your shield block rating by %d.');
        d('LOCALE_GBONUS_SPELLHIT_RATING', 'Improves spell hit rating by %d.');
        d('LOCALE_GBONUS_MELEECRIT_RATING', 'Improves melee critical strike rating by %d.');
        d('LOCALE_GBONUS_RANGEDCRIT_RATING', 'Improves ranged critical strike rating by $d.');
        d('LOCALE_GBONUS_SPELLCRIT_RATING', 'Improves spell critical strike rating by %d.');
        d('LOCALE_GBONUS_SPELLHASTE_RATING', 'Improves spell haste rating by %d.');
        d('LOCALE_GBONUS_HIT_RATING', 'Increases your hit rating by %d.');
        d('LOCALE_GBONUS_CRIT_RATING', 'Increases your critical strike rating by %d.');
        d('LOCALE_GBONUS_RESILIENCE_RATING', 'Improves your resilience rating by %d.');
        d('LOCALE_GBONUS_HASTE_RATING', 'Improves haste rating by %d.');
        d('LOCALE_GBONUS_EXPERTISE_RATING', 'Increases your expertise rating by %d.');
        d('LOCALE_GBONUS_RESTOREMANA', 'Restores %d mana per 5 sec.');
        d('LOCALE_GBONUS_ATTACKPOWER', 'Increases attack power by %d.');
        d('LOCALE_GBONUS_ARMORPENETRATION', 'Increases your armor penetration rating by %d.');
        d('LOCALE_GBONUS_SPELLPOWER', 'Improves spell power by %d.');
        d('LOCALE_GBONUS_UNKNOWN', 'Unknown Bonus #%d');
        d('LOCALE_GBONUS_CHANCEONHIT', 'Chance on hit: ');
        d('LOCALE_GBONUS_EQUIP', 'Equip: ');
        d('LOCALE_GBONUS_USE', 'Use: ');

        // misc
        d('LOCALE_SPEED', 'Speed');
        d('LOCALE_UNIQUE', 'Unique');
        d('LOCALE_START_QUEST', 'This Item Begins a Quest');
        d('LOCALE_SLOT', ' Slot ');
        d('LOCALE_DPS', 'damage per second');
        d('LOCALE_DPS2', 'damage per second');
        d('LOCALE_DPS_ADDS', 'Adds');
        d('LOCALE_ARMOR', 'Armor');
        d('LOCALE_BLOCK', 'Block');
        d('LOCALE_REQUIRES', 'Requires');
        d('LOCALE_REQUIRES_LEVEL', 'Requires Level');
        d('LOCALE_DURABILITY', 'Durability');
        d('LOCALE_CLASSES', 'Classes');
}
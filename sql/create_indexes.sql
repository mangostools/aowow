CREATE INDEX `aowow` ON `creature_loot_template`     (`mincountOrRef`);
CREATE INDEX `aowow` ON `disenchant_loot_template`   (`mincountOrRef`);
CREATE INDEX `aowow` ON `fishing_loot_template`      (`mincountOrRef`);
CREATE INDEX `aowow` ON `gameobject_loot_template`   (`mincountOrRef`);
CREATE INDEX `aowow` ON `item_loot_template`         (`mincountOrRef`);
CREATE INDEX `aowow` ON `pickpocketing_loot_template`(`mincountOrRef`);
CREATE INDEX `aowow` ON `prospecting_loot_template`  (`mincountOrRef`);
CREATE INDEX `aowow` ON `skinning_loot_template`     (`mincountOrRef`);
CREATE INDEX `aowow` ON `reference_loot_template`    (`mincountOrRef`);

CREATE INDEX `aowow_item` ON `creature_loot_template`     (`item`);
CREATE INDEX `aowow_item` ON `disenchant_loot_template`   (`item`);
CREATE INDEX `aowow_item` ON `fishing_loot_template`      (`item`);
CREATE INDEX `aowow_item` ON `gameobject_loot_template`   (`item`);
CREATE INDEX `aowow_item` ON `item_loot_template`         (`item`);
CREATE INDEX `aowow_item` ON `pickpocketing_loot_template`(`item`);
CREATE INDEX `aowow_item` ON `prospecting_loot_template`  (`item`);
CREATE INDEX `aowow_item` ON `skinning_loot_template`     (`item`);
CREATE INDEX `aowow_item` ON `reference_loot_template`    (`item`);

CREATE INDEX `aowow_lootid`         ON `creature_template` (`lootid`);
CREATE INDEX `aowow_skinloot`       ON `creature_template` (`skinloot`);
CREATE INDEX `aowow_pickpocketloot` ON `creature_template` (`pickpocketloot`);
CREATE INDEX `aowow_faction_A`      ON `creature_template` (`faction_A`);

CREATE INDEX `aowow_faction`        ON `item_template`     (`RequiredReputationFaction`);

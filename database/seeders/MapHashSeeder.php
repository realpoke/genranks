<?php

namespace Database\Seeders;

use App\Actions\CreateMapHash;
use App\Enums\GameType;
use App\Models\Map;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MapHashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding map hashes');

        $hasher = new CreateMapHash();
        $maps = [
            //'map name' => ['map crc', 'map size', 'map file', GameType::class],
            // FFA 4
            // '[RANK] Bursting Asunder ZH v1' => ['', '', 'ffa/4', GameType::FREE_FOR_ALL],
            '[RANK] Fall Out ZH v1' => ['7BE5B809', '369961', 'ffa/4', GameType::FREE_FOR_ALL],
            '[RANK] Hidden Pearls ZH v1' => ['252FD858', '149781', 'ffa/4', GameType::FREE_FOR_ALL],
            '[RANK] Volcanoe ZH v1' => ['3C26AF7F', '176614', 'ffa/4', GameType::FREE_FOR_ALL],

            // FFA 6
            '[RANK] Coral Islands ZH v1' => ['6A559B9C', '322970', 'ffa/6', GameType::FREE_FOR_ALL],
            '[RANK] Defcon Balanced ZH v1' => ['8E24F910', '312092', 'ffa/6', GameType::FREE_FOR_ALL],
            '[RANK] Naval Port Reyes ZH v1' => ['9F43E6F5', '260847', 'ffa/6', GameType::FREE_FOR_ALL],
            '[RANK] Subjugation ZH v1' => ['7FE25E16', '353076', 'ffa/6', GameType::FREE_FOR_ALL],

            // FFA 8
            '[RANK] Twister Land ZH v1' => ['BA1F5028', '205670', 'ffa/8', GameType::FREE_FOR_ALL],

            // 1v1
            '[RANK] Abandoned Desert ZH v1' => ['A691ED8A', '154830', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Abandoned Farms ZH v1' => ['3D5C30F1', '209805', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Ammars Sandcastles v3' => ['82E583DD', '128717', '1v1', GameType::ONE_ON_ONE],
            '[RANK] A New Tragedy ZH v1' => ['A2FF7198', '276449', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Annihilation' => ['941233EF', '105859', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Area J1' => ['8751FB94', '130926', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Arena of War ZH v1' => ['D0D3AE21', '81671', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Artic Lagoon' => ['269A5358', '133340', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Australia ZH v1' => ['DB06F68', '177212', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Barren Badlands Balanced ZH v2' => ['A0DCE7AD', '194291', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Battle Plan ZH v1' => ['DF69ABF8', '153043', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Bitter Winter Balanced NoCars ZH v1' => ['D0300D42', '232089', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Blossoming Valley ZH v1' => ['C33D2CF5', '171685', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Bozic Destruction ZH v3' => ['6526AC8E', '147687', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Canyon of the Dead ZH v2' => ['9E047CC1', '264038', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Cold Territory ZH v2' => ['9E047CC1', '264038', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Combat Island ZH v1' => ['EFBD4C8F', '227980', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Danger Close ZH' => ['D2BFBFC9', '134219', '1v1', GameType::ONE_ON_ONE],
            '[RANK] DeDuSu ZH v1' => ['B54C8B60', '144651', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Deserted Village v3' => ['C5E31508', '193548', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Desolated District ZH v1' => ['992C5EF2', '123561', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Devastated Oasis ZH v2' => ['F178E1AE', '145201', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Down the Road ZH v1' => ['F7610A10', '231972', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Drallim Desert ZH v2' => ['937B94F3', '77457', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Eagle Eye' => ['DC60BEC7', '122033', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Early Spring ZH v2' => ['F3369C50', '131312', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Egyptian Oasis ZH v1' => ['8C5A39CA', '139838', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Eight ZH v2' => ['160C77DC', '149125', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Embattled Land ZH v2' => ['5F28CC95', '181034', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Farmlands of the Fallen ZH v1' => ['3011D34D', '149484', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Final Crossroad ZH v1' => ['9C1276A7', '150236', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Forest of Oblivion ZH v1' => ['1B227E', '463112', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Forgotten Air Battle ZH v5' => ['DF57564E', '204218', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Freezing Rain ZH v2' => ['CA79A89E', '118190', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Glacial Shores ZH v2' => ['B3C8620C', '93961', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Gold Cobra' => ['B29AFAFC', '143121', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Hard Winter ZH v2' => ['389CD7AA', '116228', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Hidden Treasures v2' => ['3874AB3', '112640', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Highway 99 ZH v2' => ['58BABCB3', '160061', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Homeland Rocks ZH v4' => ['D96E2C51', '161052', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Imminent Victory ZH v2' => ['E671AAF2', '129321', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Irish Front ZH v1' => ['B93AEE35', '238604', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Jungle Wolf ZH v3' => ['CB5EB0F8', '216982', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Lagoon ZH v5' => ['B8FE1473', '159581', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Liquid Gold ZH v2' => ['8C6686BF', '124927', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Lost Valley v2' => ['455FB472', '102456', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Make Make 2 ZH v4' => ['D62A442F', '289492', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Melting Snow ZH v4' => ['C9BAFD3B', '160500', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Mountain Mayhem v2' => ['5EE6A116', '107791', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Mountain Oil ZH v1' => ['8F005315', '78027', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Natural Threats ZH v3' => ['EC4728BF', '158868', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Oil Oasis ZH v1' => ['15D17246', '301443', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Oil Rampage ZH v1' => ['B62705AE', '136806', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Onza Map v1' => ['E27BFB', '196780', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Planet Coyon ZH v1' => ['5ADEAD6A', '167369', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Plant Waste ZH v2' => ['9B97A1B3', '130974', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Rebellion ZH v1' => ['3D704D91', '139346', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Sand Scorpion' => ['FFFE2DB4', '164094', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Scaraa ZH v1' => ['F0DFBA7C', '134537', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Scorched Earth ZH v3' => ['711F7742', '165911', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Scorching Heat ZH v1' => ['1EFB73D8', '150515', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Sleeping Dragon v3' => ['AC9A8C43', '275353', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Snow Aggression v3' => ['AD5F8604', '245347', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Snow Blind ZH v2' => ['9B5325CC', '183086', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Snowy Drought v4' => ['2199A64E', '143274', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Sovereignty ZH v1' => ['FA3B52C3', '155921', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Stonehenge ZH v1' => ['E469929', '238810', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Storm Valley' => ['588322EE', '156138', '1v1', GameType::ONE_ON_ONE],
            '[RANK] TD Classic NoCars ZH v1' => ['632D0C7A', '144181', '1v1', GameType::ONE_ON_ONE],
            '[RANK] TD NoBugsCars ZH v1' => ['40D65C33', '144546', '1v1', GameType::ONE_ON_ONE],
            '[RANK] The Survivors ZH v1' => ['BBB340', '161324', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Total Domination No SDZ ZH v1' => ['2108FC05', '157222', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Tournament Delta ZH v2' => ['4F5B18F4', '110453', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Tournament Himalaya ZH v1' => ['2ECE4A63', '169947', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Tournament in the Canyon B ZH v2' => ['121B3157', '174182', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Uneven Heights v3' => ['5411C887', '150441', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Vendetta ZH v1' => ['7FD702FC', '231317', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Wasteland Warlords Revised' => ['CEF473E4', '299937', '1v1', GameType::ONE_ON_ONE],
            '[RANK] White Hell ZH v1' => ['BACD33CC', '129268', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Winter Arena' => ['6B722295', '128623', '1v1', GameType::ONE_ON_ONE],
            '[RANK] Winter Wolf Balanced ZH v1' => ['67D786D9', '197628', '1v1', GameType::ONE_ON_ONE],
            '[RANK] ZH Carrier is Over v2' => ['E58C4047', '134899', '1v1', GameType::ONE_ON_ONE],

            // 2v2
            '[RANK] Burning Deadline ZH v1' => ['DE0DE1DC', '234912', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Combat Encounter ZH v1' => ['BBD50431', '245198', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Conflict Zone ZH v2' => ['F1447B7A', '163037', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Desert Combat ZH v2' => ['1FAEFB10', '188985', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Drowning Horses ZH v1' => ['CD2ACA6A', '107806', '2v2', GameType::TWO_ON_TWO],
            // '[RANK] FE NoBug ZH v1' => ['', '', '2v2', GameType::TWO_ON_TWO],
            '[RANK] FE NoWall ZH v1' => ['A4CCC78B', '299466', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Final Departure ZH v1' => ['725EDF50', '301903', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Lion Heart ZH v1' => ['A914D75C', '271868', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Lost Temple ZH v1' => ['6E10F320', '240615', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Maguso ZH v3' => ['EC0FA89E', '129490', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Make Make 4 ZH v2' => ['70305F9A', '288046', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Mountain Path ZH v4' => ['FEADD509', '163068', '2v2', GameType::TWO_ON_TWO],
            // '[RANK] Rubies and Pearls ZH v1' => ['', '', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Tournament A ZH v1' => ['DC78DA90', '227282', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Unity A ZH v2' => ['6179C63B', '193928', '2v2', GameType::TWO_ON_TWO],
            '[RANK] Whispering Woods ZH v1' => ['AF873D26', '315203', '2v2', GameType::TWO_ON_TWO],

            // 3v3
            '[RANK] Icy Frontier ZH v1' => ['9620B5C3', '306005', '3v3', GameType::THREE_ON_THREE],
            '[RANK] Philippine Highlands ZH v1' => ['CEE812C', '196882', '3v3', GameType::THREE_ON_THREE],
            '[RANK] Territorial Dispute ZH v1' => ['8679F382', '284910', '3v3', GameType::THREE_ON_THREE],

            // 4v4
            '[RANK] Dusty Rampage ZH v1' => ['2259E958', '325289', '4v4', GameType::FOUR_ON_FOUR],
        ];

        foreach ($maps as $mapName => $mapMeta) {
            $mapName = Str::lower($mapName);
            $this->command->info("Adding map: {$mapName}");

            $map = new Map();
            $map = $map->updateOrCreate([
                'name' => $mapName,
            ], [
                'hash' => $hasher($mapName, $mapMeta[0], $mapMeta[1]),
                'ranked' => true,
                'file' => $mapMeta[2],
                'type' => $mapMeta[3],
            ]);

            $this->command->info("Map: {$mapName} added");
            $map->save();
        }
    }
}

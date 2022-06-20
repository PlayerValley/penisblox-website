<?php
header("Content-Type: text/plain");
if(!empty($_GET['port']))
{
    $port = $_GET['port'];

    $data = '-- StartGame -- 
game:GetService("RunService"):Run()

-- REQUIRES: StartGanmeSharedArgs.txt
-- REQUIRES: MonitorGameStatus.txt

------------------- UTILITY FUNCTIONS --------------------------

function waitForChild(parent, childName)
    while true do
        local child = parent:findFirstChild(childName)
        if child then
            return child
        end
        parent.ChildAdded:wait()
    end
end

-- returns the player object that killed this humanoid
-- returns nil if the killer is no longer in the game
function getKillerOfHumanoidIfStillInGame(humanoid)

    -- check for kill tag on humanoid - may be more than one - todo: deal with this
    local tag = humanoid:findFirstChild("creator")

    -- find player with name on tag
    if tag then
        local killer = tag.Value
        if killer.Parent then -- killer still in game
            return killer
        end
    end

    return nil
end

-- This code might move to C++
function characterRessurection(player)
    if player.Character then
        local humanoid = player.Character.Humanoid
        humanoid.Died:connect(function()
            wait(5)
            player:LoadCharacter()
            fixhealthgui(player)
        end)
    end
end

------------------- CUSTOM FUNCTIONS --------------------------

local assetPropertyNames = {"Texture", "TextureId", "SoundId", "MeshId", "SkyboxUp", "SkyboxLf", "SkyboxBk", "SkyboxRt", "SkyboxFt", "SkyboxDn", "PantsTemplate", "ShirtTemplate", "Graphic", "Image", "LinkedSource", "AnimationId"}
local variations = {"http://www%.roblox%.com/asset/%?id=", "http://www%.roblox%.com/asset%?id=", "http://%.roblox%.com/asset/%?id=", "http://%.roblox%.com/asset%?id=", "http://www%.roblox%.com/asset%?version=1%&id=", "http://www%.roblox%.com/asset/%?version=1%&id="}

function GetDescendants(o)
    local allObjects = {}
    function FindChildren(Object)
       for _,v in pairs(Object:GetChildren()) do
            table.insert(allObjects,v)
            FindChildren(v)
        end
    end
    FindChildren(o)
    return allObjects
end

function fixassets(model)
    for i, v in pairs(GetDescendants(model)) do
        for _, property in pairs(assetPropertyNames) do
            pcall(function()
                if v[property] and not v:FindFirstChild(property) then --Check for property, make sure we`re not getting a child instead of a property
                    assetText = string.lower(v[property])
                    for _, variation in pairs(variations) do
                        v[property], matches = string.gsub(assetText, variation, "http://penisblox%.ct8%.pl/asset?id=")
                        if matches > 0 then
                            break
                        end
                    end
                end
            end)
        end
    end
end

function fixhealthgui(player) -- fix the healthgui from not showing up [happens due to old roblox asset links]
    local healthgui = waitForChild(player.PlayerGui, "HealthGUI")
    fixassets(healthgui)
end

-----------------------------------END UTILITY/CUSTOM FUNCTIONS -------------------------

-----------------------------------"CUSTOM" SHARED CODE----------------------------------

pcall(function() settings().Network.UseInstancePacketCache = true end)
pcall(function() settings().Network.UsePhysicsPacketCache = true end)
--pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.FIFO end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

--settings().Network.PhysicsSend = 1 -- 1==RoundRobin
settings().Network.ExperimentalPhysicsEnabled = true
settings().Network.WaitingForCharacterLogRate = 100
pcall(function() settings().Diagnostics:LegacyScriptMode() end)

-----------------------------------START GAME SHARED SCRIPT------------------------------

local assetId = placeId -- might be able to remove this now

game:SetPlaceID(1818, false)
game:GetService("ChangeHistoryService"):SetEnabled(false)

-- establish this peer as the Server

if url~=nil then
    pcall(function() game:GetService("Players"):SetAbuseReportUrl(url .. "/AbuseReport/InGameChatHandler.ashx") end)
    pcall(function() game:GetService("ScriptInformationProvider"):SetAssetUrl(url .. "/Asset/") end)
    pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url .. "/") end)
    pcall(function() game:GetService("Players"):SetChatFilterUrl(url .. "/Game/ChatFilter.ashx") end)

    game:GetService("BadgeService"):SetPlaceId(placeId)
    if access~=nil then
        game:GetService("BadgeService"):SetAwardBadgeUrl(url .. "/Game/Badge/AwardBadge.ashx?UserID=%d&BadgeID=%d&PlaceID=%d&" .. access)
        game:GetService("BadgeService"):SetHasBadgeUrl(url .. "/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d&" .. access)
        game:GetService("BadgeService"):SetIsBadgeDisabledUrl(url .. "/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d&" .. access)

        game:GetService("FriendService"):SetMakeFriendUrl(servicesUrl .. "/Friend/CreateFriend?firstUserId=%d&secondUserId=%d&" .. access)
        game:GetService("FriendService"):SetBreakFriendUrl(servicesUrl .. "/Friend/BreakFriend?firstUserId=%d&secondUserId=%d&" .. access)
        game:GetService("FriendService"):SetGetFriendsUrl(servicesUrl .. "/Friend/AreFriends?userId=%d&" .. access)
    end
    game:GetService("BadgeService"):SetIsBadgeLegalUrl("")
    game:GetService("InsertService"):SetBaseSetsUrl(url .. "/Game/Tools/InsertAsset.php?nsets=10&type=base")
    game:GetService("InsertService"):SetUserSetsUrl(url .. "/Game/Tools/InsertAsset.php?nsets=20&type=user&userid=%d")
    game:GetService("InsertService"):SetCollectionUrl(url .. "/Game/Tools/InsertAsset.php?sid=%d")
    game:GetService("InsertService"):SetAssetUrl(url .. "/Asset/?id=%d")
    game:GetService("InsertService"):SetAssetVersionUrl(url .. "/Asset/?assetversionid=%d")
    
    pcall(function() loadfile(url .. "/Game/LoadPlaceInfo.ashx?PlaceId=" .. placeId)() end)
    
    pcall(function() 
        if access then
            loadfile(url .. "/Game/PlaceSpecificScript.ashx?PlaceId=" .. placeId .. "&" .. access)()
        end
    end)
end

pcall(function() game:GetService("NetworkServer"):SetIsPlayerAuthenticationRequired(false) end)
settings().Diagnostics.LuaRamLimit = 0

if placeId~=nil and killID~=nil and deathID~=nil and url~=nil then
    -- listen for the death of a Player
    function createDeathMonitor(player)
        -- we don`t need to clean up old monitors or connections since the Character will be destroyed soon
        if player.Character then
            local humanoid = waitForChild(player.Character, "Humanoid")
            humanoid.Died:connect(
                function ()
                    onDied(player, humanoid)
                end
            )
        end
    end

    -- listen to all Players` Characters
    game:GetService("Players").ChildAdded:connect(
        function (player)
            createDeathMonitor(player)
            player.Changed:connect(
                function (property)
                    if property=="Character" then
                        fixhealthgui(player)
                        createDeathMonitor(player)
                    end
                end
            )
        end
    )
end

game:GetService("Players").PlayerAdded:connect(function(player)
    print("Player " .. player.userId .. " added")
    
    characterRessurection(player)

    player.Changed:connect(function(name)
        if name=="Character" then
            characterRessurection(player)
            fixhealthgui(player)
        end
    end)
    
    if url and access and placeId and player and player.userId then
        game:HttpGet(url .. "/Game/ClientPresence.ashx?action=connect&" .. access .. "&PlaceID=" .. placeId .. "&UserID=" .. player.userId)
        game:HttpGet(url .. "/Game/PlaceVisit.ashx?UserID=" .. player.userId .. "&AssociatedPlaceID=" .. placeId .. "&" .. access)
    end
end)


game:GetService("Players").PlayerRemoving:connect(function(player)
    print("Player " .. player.userId .. " leaving")	

    if url and access and placeId and player and player.userId then
        game:HttpGet(url .. "/Game/ClientPresence.ashx?action=disconnect&" .. access .. "&PlaceID=" .. placeId .. "&UserID=" .. player.userId)
    end
end)

if placeId~=nil and url~=nil then
    -- yield so that file load happens in the heartbeat thread
    wait()
    
    -- load the game
    game:Load(url .. "/asset/?id=" .. placeId)
end

-- Now start the connection
game:GetService("NetworkServer"):Start('.$port.', sleeptime) 

-- ;ec death and such
function trackchat(player)
    local wordlist = {";ec", ";bleach", ";fortnite", ";cut", ";rr", ";finobe", ";deez"}
    player.Chatted:connect(function(msg)
        for index = 1, #wordlist do
            if string.lower(msg) == wordlist[index] then
                player.Character:breakJoints()
                
                local deathsound = Instance.new("Sound")
                deathsound.SoundId = "http://penisblox.ct8.pl/asset?id=1"
                deathsound.archivable = false
                deathsound.Volume = 1
                deathsound.Parent = player.Character.Head
                deathsound:Play()
            end
        end
    end)
end

game.Players.PlayerAdded:connect(function(player)
    trackchat(player)
end)
fixassets(game)
game:GetService("InsertService"):SetTrustLevel(0)
game:GetService("BadgeService"):SetPlaceId(1818)
game:GetService("BadgeService"):SetAwardBadgeUrl("http://penisblox.ct8.pl/Game/Badge/AwardBadge.php?UserID=%d&BadgeID=%d&PlaceID=%d")
game:GetService("BadgeService"):SetHasBadgeUrl("http://penisblox.ct8.pl/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d")
game:GetService("BadgeService"):SetIsBadgeDisabledUrl("http://penisblox.ct8.pl/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d")

------------------------------END START GAME SHARED SCRIPT--------------------------';

// exit
exit($data);
}
%Fe34ptwp6fQ3k2YaVhdMncWORREApt0tnVUAy5qg3n6TCCLNrZ0MrEkjB+EkpFYbYVFI/Q7XK5A/nNKdLk4FClzGbqNHiOVOJ7gfhj94h+tEiJ8yFPxWz6SXrgTzGG6F3NpEwEZ7o4lbtQv8VTrHES04fBu497rrYLXceMffZhU=%
-- Setup studio cmd bar & load core scripts
pcall(function() game:GetService("InsertService"):SetFreeModelUrl("http://penisblox.ct8.pl/Game/Tools/InsertAsset.php?type=fm&q=%s&pg=%d&rs=%d") end)
pcall(function() game:GetService("InsertService"):SetFreeDecalUrl("http://penisblox.ct8.pl/Game/Tools/InsertAsset.php?type=fd&q=%s&pg=%d&rs=%d") end)

game:GetService("ScriptInformationProvider"):SetAssetUrl("http://penisblox.ct8.pl/Asset/")
game:GetService("InsertService"):SetBaseSetsUrl("http://penisblox.ct8.pl/Game/Tools/InsertAsset.php?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("http://penisblox.ct8.pl/Game/Tools/InsertAsset.php?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("http://penisblox.ct8.pl/Game/Tools/InsertAsset.php?sid=%d")
game:GetService("InsertService"):SetAssetUrl("http://penisblox.ct8.pl/Asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("http://penisblox.ct8.pl/Asset/?assetversionid=%d")
game:GetService("InsertService"):SetTrustLevel(0)

pcall(function() game:GetService("SocialService"):SetFriendUrl("http://penisblox.ct8.pl/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("http://penisblox.ct8.pl/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("http://penisblox.ct8.pl/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRankUrl("http://penisblox.ct8.pl/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRoleUrl("http://penisblox.ct8.pl/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("GamePassService"):SetPlayerHasPassUrl("http://penisblox.ct8.pl/Game/GamePass/GamePassHandler.ashx?Action=HasPass&UserID=%d&PassID=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetProductInfoUrl("https://penisblox.ct8.pl/marketplace/productinfo?assetId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetDevProductInfoUrl("https://penisblox.ct8.pl/marketplace/productDetails?productId=%d") end)
pcall(function() game:GetService("MarketplaceService"):SetPlayerOwnsAssetUrl("https://penisblox.ct8.pl/ownership/hasasset?userId=%d&assetId=%d") end)

local starterScriptID = -1
if game.CoreGui.Version <= 2 then
	starterScriptID = 2011 --2011E
else starterScriptID = 2013 --2013E
end 

local result = pcall(function() game:GetService("ScriptContext"):AddStarterScript(starterScriptID) end)
if not result then
	pcall(function() game:GetService("ScriptContext"):AddCoreScript(starterScriptID,game:GetService("ScriptContext"),"StarterScript") end)
end
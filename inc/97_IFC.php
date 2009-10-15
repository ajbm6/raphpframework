<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

############# Motto: 'Our dependency makes slaves out of us, especially if this dependency is a dependency of our self-esteem!';
interface IFaceCommonConfigExtension extends IFaceMOD {
    // TIE: Administraiton, Authentication && Frontend;
	public function tieInWithAdministration (IFaceAdministration $objAdministrationMech);
	public function tieInWithAuthentication (IFaceAuthentication $objAuthenticationMech);
	public function tieInWithFrontend (Frontend $objFrontend);
}

interface IFaceAdministration extends IFaceCommonConfigExtension {
    // Administration
    public function tieInWithAuthenticationMechanism (IFaceAuthentication $objAuthMech);
    public function setMenuLink (S $objMenuName, FilePath $objPathToIncludedFile);
    public function setSubMLink (S $objSubMenuName, FilePath $objPathToIncludedFile);
    public function setWidget (S $objWidgetWText, B $objEVAL = NULL);
    public function setErrorMessage (S $objErrorMessage, S $objURLToGoBack);
    public function setPagination (I $objItemCount);
}

interface IFaceArticles extends IFaceCommonConfigExtension {
    // Articles
    public function getArticleCount (S $objSQLCondition = NULL);
    public function getCategoryCount (S $objSQLCondition = NULL);
    public function getArticles (S $objSQLCondition = NULL);
    public function getCategories (S $objSQLCondition = NULL);
    public function getArticlesByCategoryId (S $objCategoryId);
    public function getArticlesByCategoryName (S $objCategoryName);
    public function getArticleInfoById (S $objArticleId, S $objFieldToGet);
    public function getArticleInfoByTitle (S $objArticleTitle, S $objFieldToGet);
    public function getCategoryInfoById (S $objCategoryId, S $objFieldToGet);
    public function getCategoryInfoByName (S $objCategoryName, S $objFieldToGet);
    public function getArticlesByPage (S $objPageInt, S $objOrdering = NULL);
    public function getArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL);
    public function getArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL);
}

interface IFaceAuthentication extends IFaceCommonConfigExtension {
    // Authentication
    public function doLogIn (S $objUsername, S $objPassword, B $objDoRememberMe = NULL);
    public function doActivateByUserName (S $objUserName);
    public function doLogOut ();
    public function doMakeZone (S $objZoneName);
    public function doMapAdministratorToZone (S $objZoneName);
    public function checkCurrentUserZoneACL (S $objZoneName);
    public function checkZoneByName (S $objZoneName);
    public function checkAdministratorIsMappedtoZone (S $objZoneName);
    public function getUserCount (S $objSQLCondition = NULL);
    public function getGroupCount (S $objSQLCondition = NULL);
    public function getZoneCount (S $objSQLCondition = NULL);
    public function getMappingCount (S $objSQLCondition = NULL);
    public function getCurrentUserInfoById (S $objFieldToGet);
    public function getUserInfoById (S $objUserId, S $objFieldToGet);
    public function getUserInfoByName (S $objUserName, S $objFieldToGet);
    public function getGroupPathForCurrentUser ();
    public function getGroupInfoForCurrentUser (S $objFieldToGet);
    public function getGroupInfoByUserId (S $objUserId, S $objFieldToGet);
    public function getGroupInfoByUserName (S $objUserName, S $objFieldToGet);
    public function getGroupInfoById (S $objGroupId, S $objFieldToGet);
    public function getGroupInfoByName (S $objGroupName, S $objFieldToGet);
    public function getZoneInfoById (S $objZoneId, S $objFieldToGet);
    public function getZoneInfoByName (S $objZoneName, S $objFieldToGet);
    public function getUsers (S $objSQLCondition = NULL);
    public function getGroups (S $objSQLCondition = NULL, S $objSubCategory = NULL);
    public function getZones (S $objSQLCondition = NULL);
    public function getZoneMappings (S $objSQLCondition = NULL);
}

interface IFaceTexts extends IFaceCommonConfigExtension {
    // Texts
    public function getTexts (S $objSQLCondition = NULL);
    public function getCategories (S $objSQLCondition = NULL);
    public function getTextsByCategoryId (S $objCategoryId);
    public function getTextsByCategoryName (S $objCategoryName);
    public function getTextInfoById (S $objTextId, S $objFieldToGet);
    public function getTextInfoByTitle (S $objTextTitle, S $objFieldToGet);
    public function getCategoryInfoById (S $objCategoryId, S $objFieldToGet);
    public function getCategoryInfoByName (S $objCategoryName, S $objFieldToGet);
}

interface IFaceAudio extends IFaceCommonConfigExtension {
    // Audio
    public function getAudioFileCount (S $objSQLCondition = NULL);
    public function getCategoryCount (S $objSQLCondition = NULL);
    public function getAudioFiles (S $objSQLCondition = NULL);
    public function getCategories (S $objSQLCondition = NULL);
    public function getAudioFilesByCategoryId (S $objCategoryId, S $objSQLCondition = NULL);
    public function getAudioFilesByCategoryURL (S $objCategoryURL, S $objSQLCondition = NULL);
    public function getAudioFileInfoById (S $objAudioFileId, S $objFieldToGet);
    public function getAudioFileInfoByURL (S $objAudioFileURL, S $objFieldToGet);
    public function getCategoryInfoById (S $objCategoryId, S $objFieldToGet);
    public function getCategoryInfoByName (S $objCategoryName, S $objFieldToGet);
    public function getCategoryInfoByURL (S $objCategoryURL, S $objFieldToGet);
}
?>

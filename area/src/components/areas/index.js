import React, { useEffect, useState } from 'react';
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent, CutBarre, AreasZoneAction, ServiceNameAction, NameAction, AreasZoneReactions, ServiceNameReaction, NameReaction, ValuesReaction, AreasZoneReactionsMoovable, DeleteRappel, DeleteButtonYes, DeleteButtonNo } from './areasElements';
import { Icon } from '@iconify/react';
import { useGetInfosAreas, useDeleteInfosAreas } from '../../api/apiAreasPage';
import { useNavigate } from 'react-router-dom';

const EditAreas = () => {
  const [openArea, setOpenArea] = useState(-1);
  const [infosFromDb, setInfosFromDb] = useState([]);
  const getInfosFromDb = useGetInfosAreas();
  const deleteInfosFromDb = useDeleteInfosAreas();
  const [showDeleteRappel, setShowDeleteRappel] = useState(false);
  const navigate = useNavigate();

  useEffect(() => {
    getInfosFromDb.mutate(null, { onSuccess: (data) => { setInfosFromDb(data) } });
  }, [infosFromDb]);

  const handleEdit = (automation_id, automation_tab) => {
    navigate('/home', { state: { automationId: automation_id, automationActions: automation_tab } });
  };

  function getIcon(string) {
    switch (string) {
      case "discord":
        return "skill-icons:discord";
      case "spotify":
        return "logos:spotify-icon";
      case "instagram":
        return "skill-icons:instagram";
      case "gmail":
        return "logos:google-gmail";
      case "twitter":
        return "skill-icons:twitter";
      case "github":
        return "mdi:github";
        case "twitch":
          return "logos:twitch";
      default:
        return "mdi:github";
    }
  }

  function deleteAreas(automation_id) {
    setShowDeleteRappel(false)
    deleteInfosFromDb.mutate({ id: automation_id });
    getInfosFromDb.mutate(null, { onSuccess: (data) => { setInfosFromDb(data) } });
  };

  const automationsWithActions = infosFromDb.data?.automations.filter((automation) => automation.automation_actions.length > 0) || [];
  const containerHeight = (automationsWithActions.length * 175 + (openArea !== -1 ? 400 : 0)) + 'px';

  return (
    <>
      <BgColor BgColor />
      <NumberOfAreasText> You have {automationsWithActions.length} Area{automationsWithActions.length >= 1 ? 's' : ''} running! </NumberOfAreasText>
      <GlobalContainer height={containerHeight}>
        {automationsWithActions.map((automation, index) => {
          const top = index * 175 + (index > openArea ? (openArea !== -1 ? 400 : 0) : 0);
          const name = automation.name;
          return (
            <AreaZone top={top + "px"} key={`area${automation.id}`} >
              <AreaName> {name}</AreaName>
              {automationsWithActions[index]?.automation_actions.map((action, i) => (
                <Icon icon={getIcon(action.service)} width="70" height="70" style={{ position: 'absolute', left: `${(i+1)*150 + 500}px` }} />
              ))}
              <ArrowArea onClick={() => setOpenArea(openArea === index ? -1 : index)}>
                <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)" }} />
              </ArrowArea>
              {openArea === index &&
                <BoxContent>
                  <AreasZoneAction>
                    <ServiceNameAction> Service : {automationsWithActions[index]?.automation_actions[0]?.service}</ServiceNameAction>
                    <NameAction> Action : {automationsWithActions[index]?.automation_actions[0]?.name}</NameAction>
                  </AreasZoneAction>
                  <AreasZoneReactions>
                    {automationsWithActions[index].automation_actions.length > 2 ?
                      <div >
                        {automationsWithActions[index].automation_actions.slice(1).map((action, actionIndex) => (
                          <AreasZoneReactionsMoovable
                            key={`reaction-${actionIndex}`}
                            style={{ left: `${actionIndex * 110}%` }}>
                            <ServiceNameReaction> Reaction {actionIndex + 1} : {action.service}</ServiceNameReaction>
                            <NameReaction> Reaction : {action.name}</NameReaction>
                            <ValuesReaction> Values : {action.values}</ValuesReaction>
                          </AreasZoneReactionsMoovable>
                        ))}
                      </div> :
                      <div style={{ display: "flex", flexWrap: "wrap", justifyContent: "space-between", alignItems: "center", width: "100%" }}>
                        <ServiceNameReaction> Service : {automationsWithActions[index]?.automation_actions[1]?.service}</ServiceNameReaction>
                        <NameReaction> Reaction : {automationsWithActions[index]?.automation_actions[1]?.name}</NameReaction>
                        <ValuesReaction> Values : {automationsWithActions[index]?.automation_actions[1]?.values}</ValuesReaction>
                      </div>
                    }
                  </AreasZoneReactions>
                  <CutBarre> </CutBarre>
                  <ButtonDelete onClick={() => setShowDeleteRappel(true)}> Delete </ButtonDelete>
                  {showDeleteRappel && (
                    <DeleteRappel>
                      Do you really want to DELETE the <br></br> "{name}"" Area ?
                      <DeleteButtonYes onClick={() => deleteAreas(automationsWithActions[index].id)}> YES </DeleteButtonYes>
                      <DeleteButtonNo onClick={() => setShowDeleteRappel(false)}> NO </DeleteButtonNo>
                    </DeleteRappel>
                  )}
                  <ButtonEdit onClick={() => handleEdit(automationsWithActions[index].id, automationsWithActions[index])}> Edit </ButtonEdit>
                </BoxContent>
              }
            </AreaZone>
          );
        })}
      </GlobalContainer>
    </>
  );
};

export default EditAreas;

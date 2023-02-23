import React, { useEffect, useState } from 'react';
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent, CutBarre, AreasZoneAction, ServiceNameAction, NameAction, AreasZoneReactions, ServiceNameReaction, NameReaction, ValuesReaction, AreasZoneReactionsMoovable } from './areasElements';
import { Icon } from '@iconify/react';
import { useGetInfosAreas } from '../../api/apiAreasPage';

const EditAreas = () => {
  const [openArea, setOpenArea] = useState(-1);
  const [infosFromDb, setInfosFromDb] = useState([]);
  const getInfosFromDb = useGetInfosAreas();

  useEffect(() => {
    getInfosFromDb.mutate(null, { onSuccess: (data) => { setInfosFromDb(data) } });
  }, []);

  const automationsWithActions = infosFromDb.data?.automations.filter((automation) => automation.automation_actions.length > 0) || [];
  const containerHeight = (automationsWithActions.length * 175 + (openArea !== -1 ? 400 : 0)) + 'px';
  console.log(automationsWithActions);

  return (
    <>
      <BgColor BgColor />
      <NumberOfAreasText> You have {automationsWithActions.length} Area{automationsWithActions.length !== 1 ? 's' : ''} running! </NumberOfAreasText>
      <GlobalContainer height={containerHeight}>
        {automationsWithActions.map((automation, index) => {
          const top = index * 175 + (index > openArea ? (openArea !== -1 ? 400 : 0) : 0);
          const name = automation.name;
          return (
            <AreaZone top={top + "px"} key={`area${automation.id}`}>
              <CutBarre> </CutBarre>
              <AreaName> {name}</AreaName>
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
                      <AreasZoneReactionsMoovable>
                        {automationsWithActions[index].automation_actions.slice(1).map((action, actionIndex) => (
                          <div key={`reaction-${actionIndex}`} style={{ width: `calc(100% / ${automationsWithActions[index].automation_actions.length - 1} - 10px)`, display: "inline-block", marginRight: "1000px" }}>
                            <ServiceNameReaction> Service : {action.service}</ServiceNameReaction>
                            <NameReaction> Action : {action.name}</NameReaction>
                            <ValuesReaction> Values : {action.values}</ValuesReaction>
                          </div>
                        ))}
                      </AreasZoneReactionsMoovable> :
                      <div style={{ display: "flex", flexWrap: "wrap", justifyContent: "space-between", alignItems: "center", width: "100%" }}>
                        <ServiceNameReaction> Service : {automationsWithActions[index]?.automation_actions[1]?.service}</ServiceNameReaction>
                        <NameReaction> Action : {automationsWithActions[index]?.automation_actions[1]?.name}</NameReaction>
                        <ValuesReaction> Values : {automationsWithActions[index]?.automation_actions[1]?.values}</ValuesReaction>
                      </div>
                    }


                  </AreasZoneReactions>
                  <CutBarre> </CutBarre>
                  <ButtonDelete> Delete </ButtonDelete>
                  <ButtonEdit> Edit </ButtonEdit>
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

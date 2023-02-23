import React, { useEffect, useState } from 'react';
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent, CutBarre, AreasZoneAction, ServiceNameAction, NameAction } from './areasElements';
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
      <NumberOfAreasText> You have exactly {automationsWithActions.length} Area{automationsWithActions.length !== 1 ? 's' : ''} running! </NumberOfAreasText>
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

import React, { useEffect, useState } from 'react';
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent, CutBarre } from './areasElements';
import { Icon } from '@iconify/react';
import { useGetInfosAreas } from '../../api/apiAreasPage';

const EditAreas = () => {
  const [openArea, setOpenArea] = useState(-1);
  const [infosFromDb, setInfosFromDb] = useState([]);
  const getInfosFromDb = useGetInfosAreas();

  useEffect(() => {
    getInfosFromDb.mutate(null, { onSuccess: (data) => { setInfosFromDb(data) } });
  }, []);

  const numberOfAreas = infosFromDb.data?.automations?.length || 0;

  const areas = Array.from({ length: numberOfAreas }, (_, index) => index + 1);
  const containerHeight = (areas.length * 175 + (openArea !== -1 ? 400 : 0)) + 'px';

  return (
    <>
      <BgColor BgColor />
      <NumberOfAreasText> You have exactly {numberOfAreas} Area{numberOfAreas !== 1 ? 's' : ''} running! </NumberOfAreasText>
      <GlobalContainer height={containerHeight}>
        {areas.map((area, index) => {
          const top = index * 175 + (index > openArea ? (openArea !== -1 ? 400 : 0) : 0);
          return (
            <AreaZone top={top + "px"} key={`area${area}`}>
              <CutBarre> </CutBarre> 
              <AreaName> Area {area}</AreaName>
              <ArrowArea onClick={() => setOpenArea(openArea === index ? -1 : index)}>
                <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)" }} />
              </ArrowArea>
              {openArea === index && <BoxContent>Conteneur pour l'area {area}
                <ButtonDelete> Delete </ButtonDelete>
                <ButtonEdit> Edit </ButtonEdit>
              </BoxContent>}
            </AreaZone>
          );
        })}
      </GlobalContainer>
    </>
  );
};

export default EditAreas;

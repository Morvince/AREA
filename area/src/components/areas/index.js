import React, { useEffect, useState } from 'react';
import { AreaName, AreaZone, ArrowArea, BgColor, ButtonDelete, ButtonEdit, NumberOfAreasText, GlobalContainer, BoxContent } from './areasElements';
import { Icon } from '@iconify/react';
import { useGetInfosAreas } from '../../api/apiAreasPage';

const EditAreas = () => {
  const [areas, setAreas] = useState(Array.from({ length: 1}, (_, index) => index + 1));
  const [openArea, setOpenArea] = useState(-1);
  const [infosFromDb, setInfosFromDb] = useState([]);
  const getInfosFromDb = useGetInfosAreas();

  const containerHeight = (areas.length * 175 + (openArea !== -1 ? 400 : 0)) + 'px';

  useEffect(() => {
    getInfosFromDb.mutate(null, {onSuccess: (data)=> {setInfosFromDb(data)}});
  },[]);
  console.log(infosFromDb);

  return (
    <>
      <BgColor BgColor/>
      <NumberOfAreasText> You have exactly {areas.length} Area{areas.length !== 1 ? 's' : ''} running! </NumberOfAreasText>
      <GlobalContainer height={containerHeight}> 
        {areas.map((area, index) => {
          const top = index * 175 + (index > openArea ? (openArea !== -1 ? 400 : 0) : 0);
          return (
            <AreaZone top={top + "px"} key={`area${area}`}>
              <AreaName> Area{area}</AreaName>
              <ArrowArea onClick={() => setOpenArea(openArea === index ? -1 : index)}> 
                <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)"}} />
              </ArrowArea>
              {openArea === index && <BoxContent>Container for Area{area} <ButtonDelete> Delete</ButtonDelete> <ButtonEdit> Edit </ButtonEdit></BoxContent>}
            </AreaZone>
          );
        })}
      </GlobalContainer>
    </>
  );
};

export default EditAreas;

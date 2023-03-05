import { FlatList, View } from 'react-native';
import styles from '../styles';

export default function ActionsBar({slideServicesBar, slideActionsBar, handleStyleActionsBar, addPuzzleBlocksToList}) {
  return (
    <FlatList
      data={[
        {key: 'Discord'},
        {key: 'Spotify'},
        {key: 'Instagram'},
        {key: 'Google'},
        {key: 'Twitter'},
        {key: 'ChatGpt'},
        {key: 'John'},
        {key: 'Jillian'},
        {key: 'Jimmy'},
        {key: 'Julie'},
      ]}
      renderItem={({item}) => <View onTouchEnd={addPuzzleBlocksToList} style={[styles.actionBlocks, styles.elevation, slideActionsBar !== null ? {backgroundColor: handleStyleActionsBar[slideActionsBar][1]} : null]}/>}
      horizontal={true}
      contentContainerStyle={{height: 125, alignItems: 'center' }}
      style={[styles.actionsBar, slideActionsBar === null ? {bottom: -160} : {bottom: 75, backgroundColor: handleStyleActionsBar[slideActionsBar][0]}, !slideServicesBar && {bottom: -160}]}
    />
  )
}
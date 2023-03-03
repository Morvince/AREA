import { useEffect, useState } from 'react';
import { FlatList, View, Text, TouchableOpacity } from 'react-native';
import styles from '../styles';
import { MaterialIcons } from '@expo/vector-icons';
import { white } from '../../../color';

export default function ActionsBar({slideServicesBar, slideActionsBar, handleStyleActionsBar, addPuzzleBlocksToList, allActions, actionPuzzleBlock}) {
  const [data, setData] = useState(null)
  const [discordActions, setDiscordActions] = useState([])
  const [spotifyActions, setSpotifyActions] = useState([])
  const [twitchActions, setTwitchActions] = useState([])
  const [gmailActions, setGmailActions] = useState([])
  const [twitterActions, setTwitterActions] = useState([])
  const [githubActions, setGithubActions] = useState([])

  useEffect(() => {
    const discordActionsTmp = []
    const spotifyActionsTmp = []
    const twitchActionsTmp = []
    const gmailActionsTmp = []
    const twitterActionsTmp = []
    const githubActionsTmp = []
    if (allActions !== null) {
      allActions.forEach(element => {
        switch (element.service) {
          case "discord":
            discordActionsTmp.push(element)
            break;
          case "spotify":
            spotifyActionsTmp.push(element)
            break;
          case "twitch":
            twitchActionsTmp.push(element)
            break;
          case "gmail":
            gmailActionsTmp.push(element)
            break;
          case "twitter":
            twitterActionsTmp.push(element)
            break;
          case "github":
            githubActionsTmp.push(element)
            break;
          default:
            break;
        }
      });
      setDiscordActions(discordActionsTmp)
      setSpotifyActions(spotifyActionsTmp)
      setTwitchActions(twitchActionsTmp)
      setGmailActions(gmailActionsTmp)
      setTwitterActions(twitterActionsTmp)
      setGithubActions(githubActionsTmp)
    }
  }, [allActions])

  useEffect(() => {
    switch (slideActionsBar) {
      case "discord":
        setData(discordActions)
        break;
      case "spotify":
        setData(spotifyActions)
        break;
      case "twitch":
        setData(twitchActions)
        break;
      case "gmail":
        setData(gmailActions)
        break;
      case "twitter":
        setData(twitterActions)
        break;
      case "github":
        setData(githubActions)
        break;
      default:
        break;
    }
  }, [slideActionsBar])

  const renderItem = ({item}) => {
    return (
      <TouchableOpacity activeOpacity={0.8}>
        <View onTouchEnd={() => addPuzzleBlocksToList(item)} style={[styles.actionBlocks, styles.elevation, slideActionsBar !== null ? {backgroundColor: handleStyleActionsBar[slideActionsBar][1]} : null, actionPuzzleBlock === null && item.type === "reaction" ? {opacity: 0.5} : actionPuzzleBlock !== null && item.type === "action" ? {opacity: 0.5} : null]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
          {item.fields !== null && item.fields.length !== 0 ?
            <MaterialIcons name="keyboard-arrow-right" size={48} color={white} style={styles.arrowActionBlocks}/>
            : null
          }
        </View>
      </TouchableOpacity>
    )
  }

  return (
    <FlatList
      data={data}
      renderItem={renderItem}
      keyExtractor={item => item.id}
      horizontal={true}
      contentContainerStyle={{height: 125, alignItems: 'center' }}
      style={[styles.actionsBar, slideActionsBar === null ? {bottom: -160} : {bottom: 75, backgroundColor: handleStyleActionsBar[slideActionsBar][0]}, !slideServicesBar && {bottom: -160}]}
    />
  )
}
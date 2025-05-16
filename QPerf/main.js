document.getElementById("select-questions").addEventListener("click", () => {
  const input = document.getElementById("questions-input");
  input.click();
});

document.getElementById("questions-input").addEventListener("change", (event) => {
  const files = event.target.files;
  const fileNames = Array.from(files).map((file) => file.name).join(", ");
  document.getElementById("selected-questions").textContent = `Selected: ${fileNames || "None"}`;
  checkFilesReady();
});

document.getElementById("select-logs").addEventListener("click", () => {
  const input = document.getElementById("logs-input");
  input.click();
});

document.getElementById("logs-input").addEventListener("change", (event) => {
  const files = event.target.files;
  const fileNames = Array.from(files).map((file) => file.name).join(", ");
  document.getElementById("selected-logs").textContent = `Selected: ${fileNames || "None"}`;
  checkFilesReady();
});

document.getElementById("clear").addEventListener("click", () => {
  clear();
});

document.getElementById("run").addEventListener("click", async () => {
  // Get the selected question set files
  const questionFiles = document.getElementById("questions-input").files;
  const questionFileContents = Array.from(questionFiles).map((file) => file);

  // Get the selected quiz log files
  const logFiles = document.getElementById("logs-input").files;
  const logFileContents = Array.from(logFiles).map((file) => file);

  // Get the selected question types
  const questionTypeCheckboxes = document.querySelectorAll("#question-types input[type='checkbox']");
  const selectedQuestionTypes = Array.from(questionTypeCheckboxes)
    .filter((checkbox) => checkbox.checked)
    .map((checkbox) => checkbox.value);

  // Get the delimiter
  const delimiter = document.getElementById("delimiter").value || ",";

  // Get the tournament name
  const tournamentName = document.getElementById("tournament").value || "";

  // Get the display individual rounds option
  const displayRounds = document.getElementById("display-rounds").checked;

  // Update status message to processing
  updateStatusMessage("processing");
  // Call the qperf function
  await qperf(questionFileContents, logFileContents, selectedQuestionTypes, delimiter, tournamentName, displayRounds);
});

async function qperf(questionFiles, logFiles, questionTypes, delimiter, tournamentName, displayRounds) {
  const warns = [];

  console.log("Question Files:", questionFiles);
  console.log("Log Files:", logFiles);
  console.log("Question Types:", questionTypes);
  console.log("Delimiter:", delimiter);
  console.log("Tournament Name:", tournamentName);
  console.log("Display Rounds:", displayRounds);

  const questionTypesByRound = await getQuestionTypesByRound(questionFiles);
  console.log("Question Types by Round:", questionTypesByRound);

  const [quizRecords, quizzerNames] = await getRecords(logFiles, true, tournamentName);

  const numQuizzers = quizzerNames.length;
  const questionTypeList = ['A', 'G', 'I', 'Q', 'R', 'S', 'X', 'V', 'M'];
  const numQuestionTypes = questionTypeList.length;

  // Initialize 2D arrays for stats
  const attempts = Array.from({ length: numQuizzers }, () => Array(numQuestionTypes).fill(0));
  const correctAnswers = Array.from({ length: numQuizzers }, () => Array(numQuestionTypes).fill(0));
  const bonusAttempts = Array.from({ length: numQuizzers }, () => Array(numQuestionTypes).fill(0));
  const bonus = Array.from({ length: numQuizzers }, () => Array(numQuestionTypes).fill(0));

  // Updatable list of rounds, used to track team scores
  const rounds = [];
  updateArrays(
    warns,
    quizRecords,
    quizzerNames,
    questionTypesByRound,
    attempts,
    correctAnswers,
    bonusAttempts,
    bonus,
    true,
    rounds
  );

  let result = buildIndividualResults(
    quizzerNames,
    attempts,
    correctAnswers,
    bonusAttempts,
    bonus,
    questionTypes,
    delimiter
  );

  let teamResult = buildTeamResults(
    warns,
    rounds,
    delimiter,
    true,
    displayRounds
  );

  result += "\n" + teamResult;
  console.log("Final Result:\n", result);

  // Display warnings in the HTML
  const warningsDiv = document.getElementById("warnings");
  warningsDiv.innerHTML = warns.length
    ? warns.map(w => `<div class="warning">${w}</div>`).join("")
    : "";

  // Update status message after processing
  if (result && result.trim().length > 0) {
    updateStatusMessage("done");
  } else {
    updateStatusMessage("nooutput");
  }

  // Enable save button and store result for download
  window.qperfOutput = result;
  document.getElementById("save-output").disabled = false;
}

function updateArrays(
  warns,
  records,
  quizzerNames,
  questionTypes,
  attempts,
  correctAnswers,
  bonusAttempts,
  bonus,
  verbose,
  rounds
) {
  const missing = new Set();

  // Helper for question type indices
  const QUESTION_TYPE_INDICES = {
    'A': 0, 'G': 1, 'I': 2, 'Q': 3, 'R': 4, 'S': 5, 'X': 6, 'V': 7, 'M': 8
  };

  for (const roundName of records.keys()) {
    if (verbose) {
      console.log(`\nStarting next round: ${roundName}`);
    }
    const recordCollection = records.get(roundName);

    // Prepare round summary object
    const round = {
      round_number: recordCollection.round,
      room_number: recordCollection.room,
      team_names: recordCollection.teams.map(t => t[0]),
      team_scores: Array(recordCollection.teams.length).fill(0)
    };

    // TeamStat: { team_name, team_score, active_quizzers: [ [quizzer, correct, incorrect] ] }
    const teams = recordCollection.teams.map(team => ({
      team_name: team[0],
      team_score: 0,
      active_quizzers: []
    }));

    for (const record of recordCollection.records) {
      const columns = record.split(",");
      const eventCode = columns[10] || "";
      const quizzerName = columns[7] || "";
      const teamNumber = parseInt((columns[8] || "").replace(/'/g, ""), 10) || 0;
      const questionNumber = (parseInt((columns[5] || "0").replace(/'/g, ""), 10) || 1) - 1;

      // Find quizzer index
      const quizzerIndex = quizzerNames.findIndex(n => n[0] === quizzerName);
      // Determine question type
      let invalidQuestionType = false;
      let questionType = 'G';
      if (!questionTypes.has(recordCollection.round)) {// questionTypes is Promise<Map<Any, Any>>
        if (!missing.has(round.round_number)) {
          missing.add(round.round_number);
          invalidQuestionType = true;
        }
      }
      if (!invalidQuestionType && questionTypes.has(recordCollection.round)) {
        const qTypes = questionTypes.get(recordCollection.round);
        if ((questionNumber + 1) < qTypes.length) {
          questionType = qTypes[questionNumber];
        }
      } else if (invalidQuestionType) {
        questionType = '/';
      }

      // Memory verse types
      const memory = questionType === 'Q' || questionType === 'R' || questionType === 'V';
      const questionTypeIndex = QUESTION_TYPE_INDICES[questionType] ?? 0;

      // TeamStat helpers
      function getOrCreateTeam(idx) {
        if (!teams[idx]) {
          teams[idx] = {
            team_name: quizzerName,
            team_score: 0,
            active_quizzers: []
          };
          warns.push(`Warning: Team number ${idx} added mid-round in room ${round.room_number} round ${round.round_number}. This should not happen.`);
        }
        return teams[idx];
      }

      switch (eventCode) {
        case "'TC'": // Correct answer
          attempts[quizzerIndex][questionTypeIndex]++;
          correctAnswers[quizzerIndex][questionTypeIndex]++;
          if (memory) {
            attempts[quizzerIndex][8]++;
            correctAnswers[quizzerIndex][8]++;
          }
          {
            const team = getOrCreateTeam(teamNumber);
            team.team_score += 20;
            if (verbose) {
              console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a question right. Added 20 points to team ${team.team_name}.`);
            }
            // Add or update quizzer in active_quizzers
            let quizzer = team.active_quizzers.find(q => q[0] === quizzerName);
            if (!quizzer) {
              quizzer = [quizzerName, 1, 0];
              team.active_quizzers.push(quizzer);
            } else {
              quizzer[1]++;
              if (quizzer[1] === 4 && quizzer[2] === 0) {
                if (verbose) {
                  console.log(`[Team Scoring] Quiz-out bonus applied to team ${team.team_name}.`);
                }
                team.team_score += 10;
              }
            }
            // 3rd/4th person bonus
            if (team.active_quizzers.filter(q => q[1] > 0).length >= 3 && quizzer[1] === 1) {
              team.team_score += 10;
              if (verbose) {
                console.log(`[Team Scoring] 3rd/4th person bonus applied to team ${team.team_name}.`);
              }
            }
          }
          break;
        case "'TE'": // Incorrect answer
          attempts[quizzerIndex][questionTypeIndex]++;
          if (memory) {
            attempts[quizzerIndex][8]++;
          }
          {
            const team = getOrCreateTeam(teamNumber);
            let quizzer = team.active_quizzers.find(q => q[0] === quizzerName);
            if (quizzer) {
              quizzer[2]++;
              if (quizzer[2] === 3 || questionNumber >= 15) {
                team.team_score -= 10;
                if (verbose) {
                  console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a question wrong. Deducted 10 points from team ${team.team_name}.`);
                }
              } else if (verbose) {
                console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a question wrong. No penalty applied.`);
              }
            } else {
              if (questionNumber >= 15) {
                team.team_score -= 10;
                if (verbose) {
                  console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a question wrong. Deducted 10 points from team ${team.team_name}.`);
                }
              } else if (verbose) {
                console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a question wrong. No penalty applied.`);
              }
              team.active_quizzers.push([quizzerName, 0, 1]);
            }
          }
          break;
        case "'BC'": // Bonus correct
          bonusAttempts[quizzerIndex][questionTypeIndex]++;
          bonus[quizzerIndex][questionTypeIndex]++;
          if (memory) {
            bonusAttempts[quizzerIndex][8]++;
            bonus[quizzerIndex][8]++;
          }
          {
            const team = getOrCreateTeam(teamNumber);
            team.team_score += 10;
            if (verbose) {
              console.log(`[Team Scoring] Rm: ${round.room_number} Rd: ${round.round_number} Q: ${questionNumber + 1} Quizzer ${quizzerName} got a bonus right. Added 10 points to team ${team.team_name}.`);
            }
            if (!teams.some(t => t.active_quizzers.some(q => q[0] === quizzerName))) {
              team.active_quizzers.push([quizzerName, 0, 0]);
            }
          }
          break;
        case "'BE'": // Bonus incorrect
          bonusAttempts[quizzerIndex][questionTypeIndex]++;
          if (memory) {
            bonusAttempts[quizzerIndex][8]++;
          }
          // No team scoring change
          break;
        case "'TN'": // Team name
          getOrCreateTeam(teamNumber);
          break;
        default:
          break;
      }

      if (verbose) {
        console.log(
          `Current Round: ${round.room_number} Room: ${round.round_number} Question: ${questionNumber + 1} Current Teams: ${teams.map(t => t.team_name)} Current Scores: ${teams.map(t => t.team_score)}`
        );
      }
    }

    // Set team scores for this round
    round.team_scores = teams.map(t => t.team_score);
    rounds.push(round);
  }

  if (missing.size > 0) {
    warns.push("Warning: Some rounds are missing question sets! These questions will be treated as general!");
    warns.push(`Skipped Rounds: ${Array.from(missing).join(", ")}`);
    const foundRounds = Array.from(questionTypes.keys()).sort();
    console.warn("Found Question Sets:", foundRounds);
    warns.push("Round names must match between QuizMachine and the question set files!");
  }
}

function filterRecords(records, eventCodes = ["'TC'", "'TE'", "'BC'", "'BE'", "'TN'", "'QN'", "'RM'"]) {
  return records.filter(record => {
    const columns = record.split(",");
    return columns[10] && eventCodes.includes(columns[10].trim());
  })
}

function parseRTFFile(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();

    reader.onload = (event) => {
      try {
        const content = event.target.result;
        const roundRegex = /SET #([A-Za-z0-9]+)/g;
        const parts = content.split("\\tab");
        //console.log(parts);
        const questionTypesByRound = new Map();

        let currentRound = null;
        let questionTypes = [];

        parts.forEach((part, index) => {
          const match = roundRegex.exec(part);
          if (match) {
            // If a new round is found, save the previous round's data
            if (currentRound && questionTypes.length > 0) {
              questionTypesByRound.set(currentRound, questionTypes);
            }

            // Start a new round
            currentRound = `'${match[1]}'`; // Format round number like Rust
            questionTypes = [];
          }

          // Extract question types (every second part contains question type info)
          if (index % 2 === 0 && part.trim().length > 0) {
            //console.log("Possible question type in part: ", part);
            const chars = part.trim().split("");
            //Stop adding question types if the current round already has 20
            if (chars.length > 1 && questionTypes.length < 20) {
              questionTypes.push(chars[chars.length - 1]);
              //console.log("Extracted question type: ", chars[chars.length - 1]);
            }
          }
        });

        // Save the last round's data
        if (currentRound && questionTypes.length > 0) {
          questionTypesByRound.set(currentRound, questionTypes);
        }

        // Validate round names
        for (const [round, _types] of questionTypesByRound) {
          if (round === "''") {
            console.warn(
              "Warning: RTF question set file might have been formatted incorrectly. Please use only the original RTF files!"
            );
          }
        }

        resolve(questionTypesByRound);
      } catch (error) {
        reject(`Error parsing RTF file: ${error.message}`);
      }
    };

    reader.onerror = (error) => {
      reject(`Error reading file: ${error.message}`);
    };

    reader.readAsText(file);
  });
}

async function getQuestionTypesByRound(rtfFiles) {
  const questionTypesByRound = new Map();

  for (const file of rtfFiles) {
    try {
      const questionTypes = await parseRTFFile(file);

      // Merge the results into the main map
      for (const [roundNumber, questionTypesArray] of questionTypes.entries()) {
        if (questionTypesByRound.has(roundNumber)) {
          console.warn(
            `Warning: Duplicate question set number: ${roundNumber}. Using only the first occurrence.`
          );
        } else {
          questionTypesByRound.set(roundNumber, questionTypesArray);
        }
      }
    } catch (error) {
      console.error(`Error processing file ${file.name}: ${error}`);
    }
  }

  return questionTypesByRound;
}

/**
 * Checks if a new round has started by comparing the current round and room to the columns of the current record.
 * @param {string} round - The current round string.
 * @param {string} room - The current room string.
 * @param {string[]} columns - The columns of the current record.
 * @returns {boolean}
 */
function checkNewRound(round, room, columns) {
  // columns[4] = round, columns[3] = room
  return round !== (columns[4] || "") || room !== (columns[3] || "");
}

/**
 * Confirms teams and quizzers for a round if action has occurred.
 * @param {Array<[string, string[]]>} roundTeams - Array of [teamName, quizzerNames[]].
 * @param {string[]} confirmedTeams - Array of confirmed team names.
 * @param {Array<[string, string]>} confirmedQuizzers - Array of [quizzerName, teamName].
 * @param {boolean} verbose - Whether to log debug info.
 * @param {Object} actionRef - Object with a boolean 'value' property, used as a mutable reference.
 * @returns {boolean} - True if round is valid and confirmed, false otherwise.
 */
function checkValidRound(roundTeams, confirmedTeams, confirmedQuizzers, verbose, actionRef) {
  let valid = false;
  if (actionRef.value) {
    for (let i = 0; i < roundTeams.length; i++) {
      if (!confirmedTeams.includes(roundTeams[i][0])) {
        confirmedTeams.push(roundTeams[i][0]);
      }
      for (let j = 0; j < roundTeams[i][1].length; j++) {
        const quizzer = roundTeams[i][1][j];
        if (
          !confirmedQuizzers.some(([q, _]) => q === quizzer) &&
          quizzer !== "''" &&
          quizzer !== ""
        ) {
          confirmedQuizzers.push([quizzer, roundTeams[i][0]]);
        }
      }
    }
    if (verbose) {
      console.log("Confirming Teams:", roundTeams);
    }
    valid = true;
  } else {
    if (verbose) {
      console.log("No action taken in round, teams:", roundTeams, "might be from practice");
    }
  }
  actionRef.value = false;
  return valid;
}

/**
 * @param {string[]} records - Array of CSV row strings.
 * @param {boolean} verbose
 * @param {Array<string>} warns - Array to collect warning messages.
 * @returns {[Array<[string, string]>, Map<string, Object>]}
 */
function getQuizzerNames(records, verbose = false, warns = []) {
  let roundTeams = [];
  let roundString = "";
  let roomString = "";
  let confirmedQuizzers = [];
  let confirmedTeams = [];
  let actionRef = { value: false };

  let confirmedRecords = new Map();
  let candidateRecords = [];
  let matchString = "";

  for (const record of records) {
    if (verbose) console.log(record);

    const columns = record.split(",");
    const ecode = columns[10] || "";
    const name = columns[7] || "";

    const teamNumberString = (columns[8] || "").replace(/'/g, "");
    const teamNumber = parseInt(teamNumberString, 10) || 0;

    const seatNumberString = (columns[9] || "").replace(/'$/, "");
    const seatNumber = parseInt(seatNumberString, 10) || 0;

    if (checkNewRound(roundString, roomString, columns)) {
      if (checkValidRound(roundTeams, confirmedTeams, confirmedQuizzers, verbose, actionRef)) {
        // Remove teams with no name
        roundTeams = roundTeams.filter(t => t[0] !== "''" && t[0] !== "");
        // Remove quizzers with no name
        roundTeams.forEach(team => {
          team[1] = team[1].filter(q => q !== "''" && q !== "");
        });

        confirmedRecords.set(matchString, {
          room: roomString,
          round: roundString,
          teams: JSON.parse(JSON.stringify(roundTeams)),
          records: candidateRecords.slice()
        });

        if (verbose) {
          const teamNames = roundTeams.map(t => t[0]);
          console.log(`Confirming round ${matchString} with teams`, roundTeams, "and", candidateRecords.length, "records");
        }
      }
      candidateRecords = [];
      roundTeams = [];

      matchString = "Rm" + (columns[3] || "").replace(/'/g, "") + "Rd" + (columns[4] || "").replace(/'/g, "");
      roomString = columns[3] || "";
      roundString = columns[4] || "";
    }

    if (ecode === "'TN'") {
      while (roundTeams.length <= teamNumber) {
        roundTeams.push(["", []]);
      }
      roundTeams[teamNumber] = [name, []];
      if (verbose) console.log(`Set team number ${teamNumber} to ${name}`);
    } else if (ecode === "'QN'") {
      while (roundTeams.length <= teamNumber) {
        roundTeams.push(["", []]);
      }
      while (roundTeams[teamNumber][1].length <= seatNumber) {
        roundTeams[teamNumber][1].push("");
      }
      roundTeams[teamNumber][1][seatNumber] = name;
      if (verbose) {
        console.log(`Set seat number ${seatNumber} to ${name} for ${roundTeams[teamNumber][0]}`);
        console.log("Current lineup:", roundTeams.map(t => `${t[0]} ${JSON.stringify(t[1])}`).join(" | "));
      }
    } else if (
      ecode === "'BC'" ||
      ecode === "'BE'" ||
      ecode === "'TC'" ||
      ecode === "'TE'"
    ) {
      actionRef.value = true;
      candidateRecords.push(record);
      if (verbose) console.log("Action happened during this round. It's probably not junk data.");
    }
  }

  // Check last round
  if (verbose) console.log(`Checking last round, ${candidateRecords.length} records remaining`);
  if (checkValidRound(roundTeams, confirmedTeams, confirmedQuizzers, verbose, actionRef)) {
    confirmedRecords.set(matchString, {
      room: roomString,
      round: roundString,
      teams: JSON.parse(JSON.stringify(roundTeams)),
      records: candidateRecords.slice()
    });
    if (verbose) {
      const teamNames = roundTeams.map(t => t[0]);
      console.log(`Confirming round ${matchString} with teams`, roundTeams, "and", candidateRecords.length, "records");
    }
  }

  if (verbose) {
    console.log("Confirmed Teams:", confirmedTeams);
    console.log("Confirmed Quizzers:", confirmedQuizzers);
  }

  return [confirmedQuizzers, confirmedRecords];
}

/**
 * Reads and processes CSV files, filters records by tournament name, and extracts quizzer/team/round structure.
 * @param {File[]} csvFiles - Array of File objects (CSV files).
 * @param {boolean} verbose
 * @param {string} tourn - Tournament name to filter by (can be empty).
 * @param {Array<string>} warns - Array to collect warning messages.
 * @returns {Promise<[Map<string, Object>, Array<[string, string]>]>}
 */
async function getRecords(csvFiles, verbose = false, tourn = "", warns = []) {
  let quizRecords = [];

  // Read all CSV files and collect all records as strings
  for (const file of csvFiles) {
    try {
      const content = await file.text();
      // Split into lines, filter out empty lines
      const lines = content.split(/\r?\n/).filter(line => line.trim().length > 0);
      for (const line of lines) {
        quizRecords.push(line);
      }
      if (verbose) {
        console.log(`Read ${lines.length} records from file ${file.name}`);
      }
    } catch (e) {
      warns.push(`Quiz data contains formatting error in file ${file.name}: ${e}`);
      if (verbose) console.error(e);
    }
  }

  const countRecords = quizRecords.length;

  // Filter records by event code and tournament name
  const filteredRecords = quizRecords.filter(record => {
    const columns = record.split(",");
    // Tournament name is column 1 (index 1)
    if (tourn && tourn !== (columns[1] || "")) {
      return false;
    }
    // Event code is column 10 (index 10)
    const eventCodes = ["'TC'", "'TE'", "'BC'", "'BE'", "'TN'", "'QN'", "'RM'"];
    return columns[10] && eventCodes.includes(columns[10].trim());
  });

  if (filteredRecords.length === 0 && countRecords > 0) {
    warns.push(`Warning: No records found for tournament ${tourn}`);
  }
  if (verbose) {
    console.log(`Found ${filteredRecords.length} filtered records`);
  }

  // Get quizzer names and round structure
  const [quizzerNames, records] = getQuizzerNames(filteredRecords, verbose, warns);

  if (verbose) {
    console.log("Quizzer Names:", quizzerNames);
  }

  // Return as [recordsByRound, quizzerNames]
  return [records, quizzerNames];
}

/**
 * Builds the individual results CSV string.
 * @param {Array<[string, string]>} quizzerNames - Array of [quizzerName, teamName] pairs.
 * @param {number[][]} attempts
 * @param {number[][]} correctAnswers
 * @param {number[][]} bonusAttempts
 * @param {number[][]} bonus
 * @param {string[]} types - Array of question type chars to include (e.g. ['A','G',...])
 * @param {string} delim - Delimiter (e.g. ",")
 * @returns {string}
 */
function buildIndividualResults(quizzerNames, attempts, correctAnswers, bonusAttempts, bonus, types, delim) {
  let result = "";

  // Build the header
  result += "Quizzer" + delim + "Team" + delim;
  // Sorted question types
  const questionTypeList = ['A', 'G', 'I', 'Q', 'R', 'S', 'X', 'V', 'M'].filter(qt => types.length === 0 || types.includes(qt));
  for (const qt of questionTypeList) {
    result += `${qt} Attempted${delim}${qt} Correct${delim}${qt} Bonuses Attempted${delim}${qt} Bonuses Correct${delim}`;
  }
  result += "\n";

  // Build the results for each quizzer
  for (let i = 0; i < quizzerNames.length; i++) {
    // Remove single quotes if present
    const quizzerName = quizzerNames[i][0].replace(/^'+|'+$/g, "");
    const team = quizzerNames[i][1].replace(/^'+|'+$/g, "");
    result += `${quizzerName}${delim}${team}${delim}`;
    for (const qt of questionTypeList) {
      // Indices: A=0, G=1, ..., M=8
      const idx = {A:0,G:1,I:2,Q:3,R:4,S:5,X:6,V:7,M:8}[qt] ?? 0;
      result += `${attempts[i][idx]}${delim}${correctAnswers[i][idx]}${delim}${bonusAttempts[i][idx]}${delim}${bonus[i][idx]}${delim}`;
    }
    result += "\n";
  }

  return result;
}

/**
 * Builds the team results CSV string, including round-by-round and final rankings.
 * @param {Array<string>} warns - Array to collect warning messages.
 * @param {Array<Object>} rounds - Array of round objects ({room_number, round_number, team_names, team_scores}).
 * @param {string} delim - Delimiter (e.g. ",")
 * @param {boolean} verbose
 * @param {boolean} displayRounds
 * @returns {string}
 */
function buildTeamResults(warns, rounds, delim, verbose, displayRounds) {
  let result = "";

  if (verbose) {
    console.log(`Beginning to process ${rounds.length} rounds for team standing`);
  }

  // Display the results of each individual round
  if (displayRounds) {
    result += "Individual Round Results\n\n";
    for (const round of rounds) {
      result += `Room: ${round.room_number}${delim} Round: ${round.round_number}\n`;
      for (let i = 0; i < round.team_names.length; i++) {
        result += `${round.team_names[i]}${delim} ${round.team_scores[i]}\n`;
      }
      result += "\n";
    }
    result += "\n";
  }

  // --- Final Rankings ---
  // Calculate team points, wins, losses, total scores, and head-to-heads
  const teamsSet = new Set();
  const wins = {};
  const losses = {};
  const totalScores = {};
  const headToHead = {};

  // Collect all teams and initialize stats
  for (const round of rounds) {
    for (let i = 0; i < round.team_names.length; i++) {
      const team = round.team_names[i];
      if (!team || team === "''") continue;
      teamsSet.add(team);
      wins[team] = wins[team] || 0;
      losses[team] = losses[team] || 0;
      totalScores[team] = (totalScores[team] || 0) + (round.team_scores[i] || 0);
    }
  }

  // Helper for head-to-head key
  function matchupKey(teamA, teamB) {
    return [teamA, teamB].sort().join("::");
  }

  // Process match results
  for (const round of rounds) {
    // Only consider non-empty teams
    const scoredTeams = round.team_names
      .map((team, idx) => ({ team, score: round.team_scores[idx] }))
      .filter(t => t.team && t.team !== "''");

    if (scoredTeams.length < 2) continue;

    // Sort by score descending
    scoredTeams.sort((a, b) => b.score - a.score);

    // Wins/losses
    for (const t1 of scoredTeams) {
      for (const t2 of scoredTeams) {
        if (t1.team === t2.team) continue;
        if (t1.score > t2.score) wins[t1.team]++;
        else if (t1.score < t2.score) losses[t1.team]++;
      }
    }

    // Head-to-head
    if (scoredTeams.length === 2) {
      // Two-team round
      const [a, b] = scoredTeams;
      const key = matchupKey(a.team, b.team);
      headToHead[key] = headToHead[key] || [0, 0];
      if (a.team < b.team) {
        headToHead[key][0] += a.score;
        headToHead[key][1] += b.score;
      } else {
        headToHead[key][0] += b.score;
        headToHead[key][1] += a.score;
      }
    } else if (scoredTeams.length === 3) {
      // Three-team round: all pairs
      const [a, b, c] = scoredTeams;
      const pairs = [
        [a, b],
        [a, c],
        [b, c]
      ];
      for (const [t1, t2] of pairs) {
        const key = matchupKey(t1.team, t2.team);
        headToHead[key] = headToHead[key] || [0, 0];
        if (t1.team < t2.team) {
          headToHead[key][0] += t1.score;
          headToHead[key][1] += t2.score;
        } else {
          headToHead[key][0] += t2.score;
          headToHead[key][1] += t1.score;
        }
      }
    }
  }

  // Build ranking array
  let ranking = Array.from(teamsSet).map(team => ({
    team,
    placement: 0,
    wins: wins[team] || 0,
    losses: losses[team] || 0,
    totalScore: totalScores[team] || 0
  }));

  // Sort: losses ASC, wins DESC, head-to-head as tiebreaker
  ranking.sort((a, b) => {
    if (a.losses !== b.losses) return a.losses - b.losses;
    if (a.wins !== b.wins) return b.wins - a.wins;
    // Head-to-head tiebreaker
    const key = matchupKey(a.team, b.team);
    const h2h = headToHead[key] || [0, 0];
    // Lower team name is always first in key
    if (a.team < b.team) return h2h[1] - h2h[0];
    else return h2h[0] - h2h[1];
  });

  // Assign placement
  ranking.forEach((entry, idx) => {
    entry.placement = idx + 1;
  });

  result += "Team Results\n\n";
  result += "Teams are ranked first by number of losses, then by number of wins, then by head-to-head record. \n\n";
  result += `Name${delim}Placement${delim}Wins${delim}Losses${delim}Total Score\n`;
  for (const entry of ranking) {
    const teamName = entry.team.replace(/^'+|'+$/g, "");
    result += `${teamName}${delim}${entry.placement}${delim}${entry.wins}${delim}${entry.losses}${delim}${entry.totalScore}\n`;
  }

  return result;
}

// Utility to update the status message with helpful hints
function updateStatusMessage(stage = "init") {
  const statusDiv = document.getElementById("status-message");
  switch (stage) {
    case "init":
      statusDiv.textContent = "Waiting for input files. Please select both a question set (RTF) and quiz logs (CSV) to begin.";
      break;
    case "ready":
      statusDiv.textContent = "Files loaded! Set question types and settings. When ready, click 'Run' to process your data.";
      break;
    case "processing":
      statusDiv.textContent = "Processing files... Please wait.";
      break;
    case "done":
      statusDiv.textContent = "Output generated! Click 'Save Output' to download your CSV file. You may adjust settings and run again if needed.";
      break;
    case "nooutput":
      statusDiv.textContent = "No output generated. Please check your input files and settings.";
      break;
    default:
      statusDiv.textContent = "";
  }
}

// Update status on file selection
function checkFilesReady() {
  const questionsInput = document.getElementById("questions-input");
  const logsInput = document.getElementById("logs-input");
  const runButton = document.getElementById("run");

  // Log the actual FileList objects for debugging
  console.log("Questions input files:", questionsInput.files);
  console.log("Logs input files:", logsInput.files);

  // Check if at least one file is selected for each input
  const questionsSelected = questionsInput.files && questionsInput.files.length > 0;
  const logsSelected = logsInput.files && logsInput.files.length > 0;

  console.log("Questions selected:", questionsSelected);
  console.log("Logs selected:", logsSelected);

  if (questionsSelected && logsSelected) {
    updateStatusMessage("ready");
    runButton.disabled = false;
  } else {
    updateStatusMessage("init");
    runButton.disabled = true;
  }
}

// On page load, set initial status message and disable run button
clear();
updateStatusMessage("init");
document.getElementById("run").disabled = true;

document.getElementById("save-output").addEventListener("click", () => {
  const output = window.qperfOutput || "";
  const blob = new Blob([output], { type: "text/csv" });
  const url = URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.href = url;
  a.download = "qperf_output.csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});

function clear() {
  // Clear file selections
  document.getElementById("questions-input").value = "";
  document.getElementById("logs-input").value = "";
  document.getElementById("selected-questions").textContent = "Selected: None";
  document.getElementById("selected-logs").textContent = "Selected: None";
  checkFilesReady();


  // Reset question type checkboxes
  const questionTypeCheckboxes = document.querySelectorAll("#question-types input[type='checkbox']");
  questionTypeCheckboxes.forEach((checkbox) => {
    checkbox.checked = true;
  });

  // Reset delimiter and tournament name
  document.getElementById("delimiter").value = "";
  document.getElementById("tournament").value = "";

  // Reset 'display individual rounds' checkbox
  document.getElementById("display-rounds").checked = false;
}